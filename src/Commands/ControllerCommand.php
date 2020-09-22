<?php

namespace Olatunji\ControllerGenerator;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


class ControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'controller:generate {tables* : Names of tables which Controllers are to be generated for} {--nofiles : Specifies if fields do not contain files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Controllers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Your Majesty!');
        $this->line('Beginning the process Your Greatness ...');
        foreach($this->argument('tables') as $table){
            
            if(Schema::hasTable($table)){
                if($this->option('nofiles')==''){
                
                $colw=DB::getSchemaBuilder()->getColumnListing($table);
                $donotinclude =['id','created_at','updated_at'];
                $colw=array_diff($colw,$donotinclude);
                $continue = 0;
                $i=0;
                $files=[];
                if($this->confirm('Are there files here, Your Highness?')){
                    do{
                    array_push($files,$this->choice('Which one is it Great One?', array_diff($colw,$files)));
                    $i++;
                    if($this->confirm('Any more Venerable One?')){

                    }else{
                        $continue=1;
                    }
                    }while($continue==0 && $i<count($colw));
                }
            }
                $columns=[];
                $stub = file_get_contents(__DIR__ . '/../Stubs/controller.stub');
                $cols=array_diff($colw,$files);
                foreach ($files as $key => $column) {
                    $column = Schema::getConnection()->getDoctrineColumn($table,$column);
                    $type=$column->getType();
                    $notNull=$column->getNotnull();
                    $length=$column->getLength();

                    $replacement='
                    if($request->hasFile("'.$column->getName().'")){
                    $fileNameWithExt=$request->file("'.$column->getName().'")->getClientOriginalName();
                    $fileName=pathinfo($fileNameWithExt,PATHINFO_FILENAME);
                    $extension=$request->file("'.$column->getName().'")->getClientOriginalExtension();
                    $fileNameToStore=$fileName."_".time().".".$extension;
                    $path=$request->file("'.$column->getName().'")->storeAs("public/{{table_name}}/",$fileNameToStore);
                    
                            }else{
                                $path="noimage.jpg";
                            }

                            ${{table_name}}->'.$column->getName().' = asset("public/storage/{{table_name}}/".$fileNameToStore);
                            {{fields_code}}';
                            

                $stub = str_replace('{{fields_code}}',$replacement,$stub);
                $required = ($notNull==true)?'required':'nullable';
                $validate = '"'.$column->getName().'"=>"'.$required.'|file|max:1999",';
                $stub = str_replace('{{validate}}',$validate.'
                {{validate}}',$stub);
                $rep='if(${{table_name}}->'.$column->getName().'!="noimage.jpg"){
                    Storage::delete(str_replace(url("/")."/","",${{table_name}}->'.$column->getName().'));
                };';
                $stub = str_replace('{{file_delete}}',$rep.'
                {{file_delete}}',$stub);
                }

                foreach ($cols as $key => $column) {
                    $column = Schema::getConnection()->getDoctrineColumn($table,$column);
                    $type=$column->getType();
                    $notNull=$column->getNotnull();
                    $length=$column->getLength();
                    $replacement ='
                    ${{table_name}}->'.$column->getName().' = $request->input("'.$column->getName().'");
                    {{fields_code}}';
                            $stub = str_replace('{{fields_code}}',$replacement,$stub);
                            $required = ($notNull==true)?'required':'nullable';
                            $validate = '"'.$column->getName().'"=>"'.$required.'|max:'.$length.'",';
                            $stub = str_replace('{{validate}}',$validate.'
                            {{validate}}',$stub);  
                            $stub = str_replace('{{search_fields}}','"'.$column->getName().'",{{search_fields}}',$stub);  
                             $other_search_options='else if($k=="'.$column->getName().'"){
                                $res=(new Search())
                                        ->registerModel(Attribute::class,["'.$column->getName().'"])->search($query);
                            }';
                            $stub = str_replace('{{other_search_options}}',$other_search_options.
                            '{{other_search_options}}',$stub);  
                }
                
                $table_name =$this->string($table);
                $stub = str_replace('{{paginate}}','paginate(10)',$stub);
                $stub = str_replace('{{fields_code}}','${{table_name}}->save();',$stub);
                $stub = str_replace('{{validate}}','',$stub);
                $stub = str_replace('{{file_delete}}','',$stub);
                $stub = str_replace('{{search_fields}}','',$stub);
                $stub = str_replace('{{other_search_options}}','',$stub);
                $Template = $this->template($table,$stub,'table');
                file_put_contents(app_path("Http/Controllers/".$table_name['StringNames'].'Controller.php'), $Template);
                
            }else{
                $this->error('Table '.$table.' does not exist'); 
            }
            
            $this->line("Creating Controller for ".ucwords($table).' ...');
            $this->initSpatieSearchable($table);

            $this->line(ucwords($table).' Controller Created ...');
        }
        
        $this->initUxwebSweetAlert();
        // $password = $this->secret('What is the password?');
        // $name = $this->choice('What is your name?', ['Taylor', 'Dayle'], 0);
        // $this->confirm('Do you wish to continue?');
        // $this->info('Display this on the screen');
        // $this->error('Display this on the screen');
        // $this->line('Display this on the screen');
        return 0;
    }

    protected function initUxwebSweetAlert(){
        $app_file=file_get_contents(config_path('app.php'));
        $serviceProvider='UxWeb\SweetAlert\SweetAlertServiceProvider::class';
        $alias=" 'Alert' => UxWeb\SweetAlert\SweetAlert::class,";
        if(strpos($app_file,$serviceProvider)==false && strpos($app_file,$alias)==false){
            $this->line('Initializing uxweb/sweet-alert in config/app.php');
        $app_file=str_replace('App\Providers\RouteServiceProvider::class','App\Providers\RouteServiceProvider::class,
        '.$serviceProvider,$app_file);
        $app_file=str_replace("'App' => Illuminate\Support\Facades\App::class,","
       ".$alias."
        'App' => Illuminate\Support\Facades\App::class,",$app_file);
file_put_contents(config_path('app.php'),$app_file);
$this->info('uxweb/sweet-alert initialized');        
}
        
    }


    protected function initSpatieSearchable($table){

        if(directoryExists(app_path('/Models'))){
            $this->line('Initializing spatie/laravel-searchable in Model '.$this->string($table)['StringName'].'.php');
            $filedir=app_path('/Models/'.$this->string($table)['StringName'].'.php');
            function replacemodel($filedir,$table){
                $model =file_get_contents($filedir);
                $find ='}';
                $replace='
                
                public function getSearchResult() : SearchResult
                {
                    $url = route("'.$table.'/search?q=", $this->slug);
                    return new SearchResult(
                        $this,
                        $this->id,
                        $url
                    );
                } 
            }
                ';
                $result = strrev(preg_replace(strrev("/$find/"),strrev($replace),strrev($model),1));
    $rep = 
    'namespace App\Models;
    
    use Spatie\Searchable\Searchable;
    use Spatie\Searchable\SearchResult;';
    $result = str_replace('namespace App\Models;',$rep,$result);
                file_put_contents($filedir,$result);
             }
                        if(file_exists($filedir)){
                            replacemodel($filedir,$table);
             }else{
                 $this->error('No Model found for '.$table);
                $this->call('make:model',['name'=>$this->string($table)['StringName']]);
                replacemodel($filedir,$table);
             }
             $this->info('spatie/laravel-searchable initialized');
        }
    }

    protected function string($string) {

        $string_name = Str::snake(Str::singular(Str::lower($string)));
        $string_names = Str::snake(Str::plural(Str::lower($string)));
        $String_Name = Str::snake(Str::singular(ucwords($string)));
        $String_Names = Str::snake(Str::plural(ucwords($string)));
        $string_name_ = Str::singular(Str::lower($string));
        $string_names_ = Str::plural(Str::lower($string));
        $String_Name_ = Str::singular(ucwords($string));
        $String_Names_ = Str::plural(ucwords($string));
        $stringname = str_replace(' ', '',Str::singular(Str::lower($string)));
        $stringnames = str_replace(' ', '',Str::plural(Str::lower($string)));
        $StringName = str_replace(' ', '',Str::singular(ucwords($string)));
        $StringNames = str_replace(' ', '',Str::plural(ucwords($string)));
        return [
'string'=> $string,
'string_name' => $string_name,
'string_names' => $string_names,
'String_Name' => $String_Name,
'String_Names' => $String_Names,
'string name' => $string_name_,
'string names' => $string_names_,
'String Name' => $String_Name_,
'String Names' => $String_Names_,
'stringname' => $stringname,
'stringnames' => $stringnames,
'StringName' => $StringName,
'StringNames' => $StringNames

        ];
    }




    protected function template($table_name, $stub,$string) {
        $table_name=$this->string($table_name);
        $template = str_replace(
            '{{'.$string.'}}',
            $table_name['string'],
            $stub);
        $template = str_replace(
            '{{'.$string.'_name}}',
            $table_name['string_name'],
            $template);
        $template = str_replace(
            '{{'.$string.'_names}}',
            $table_name['string_names'],
            $template);
        $template = str_replace(
            '{{'.ucwords($string).'_Name}}',
            $table_name['String_Name'],
            $template);
        $template = str_replace(
            '{{'.ucwords($string).'_Names}}',
            $table_name['String_Names'],
            $template);
        $template = str_replace(
            '{{'.$string.' name}}',
            $table_name['string name'],
            $template);
        $template = str_replace(
            '{{'.$string.' names}}',
            $table_name['string names'],
            $template);
        $template = str_replace(
            '{{'.ucwords($string).' Name}}',
            $table_name['String Name'],
            $template);
        $template = str_replace(
            '{{'.ucwords($string).' Names}}',
            $table_name['String Names'],
            $template);
        $template = str_replace(
            '{{'.$string.'name}}',
            $table_name['stringname'],
            $template);
        $template = str_replace(
            '{{'.$string.'names}}',
            $table_name['stringnames'],
            $template);
        $template = str_replace(
            '{{'.ucwords($string).'Name}}',
            $table_name['StringName'],
            $template);
        $template = str_replace(
            '{{'.ucwords($string).'Names}}',
            $table_name['StringNames'],
            $template);
        return $template;
    }




}
