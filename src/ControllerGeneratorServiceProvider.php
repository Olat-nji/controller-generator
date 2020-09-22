<?php
namespace Olatunji\ControllerGenerator;


use Illuminate\Support\ServiceProvider;

class ControllerGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            'Olatunji\ControllerGenerator\ControllerCommand'
        );

    }
}
