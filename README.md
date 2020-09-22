# Laravel CRUD Controller Generator
Laravel Package that helps Ease up the task of generating controllers with similar code structure

### Requirements
    Laravel >=5.5
    PHP >= 7.3

## Features
- Generate Controller with pre-written --resource code
- Validate Incoming requests in Store And Update
- File Upload 
- Packed with [uxweb/sweet-alert](https://github.com/uxweb/sweet-alert)
- Search function implemented with [spatie/laravel-searchable](https://github.com/spatie/laravel-searchable)
 

## Installation

1. Run
    ```
    composer require olat-nji/controller-generator
    ```

2. Create your controllers.
    ```
    php artisan controller:generate blog students
    ```
    ```
    php artisan controller:generate quotes --noimage
    ```
    > Service provider will be discovered automatically.

3. Follow instructions for uxweb and laravel-searchable
  


<!-- ## Usage

1. Create some permissions.

2. Create some roles.

3. Assign permission(s) to role.

4. Create user(s) with role.

5. For checking authenticated user's role see below:
    ```php
    // Add roles middleware in app/Http/Kernel.php
    protected $routeMiddleware = [
        ...
        'roles' => \App\Http\Middleware\CheckRole::class,
    ];
    ```

    ```php
    // Check role anywhere
    if (Auth::check() && Auth::user()->hasRole('admin')) {
        // Do admin stuff here
    } else {
        // Do nothing
    }

    // Check role in route middleware
    Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth', 'roles'], 'roles' => 'admin'], function () {
       Route::get('/', ['uses' => 'AdminController@index']);
    });
    ```

6. For checking permissions see below:

    ```php
    if ($user->can('permission-name')) {
        // Do something
    }
    ```
 -->


<!-- For activity log please read `spatie/laravel-activitylog` [docs](https://docs.spatie.be/laravel-activitylog/v2/introduction) -->

<!-- ## Screenshots

![users](https://user-images.githubusercontent.com/1708683/43477093-1ac08d42-951c-11e8-8217-00aedc19b28d.png)

![activity log](https://user-images.githubusercontent.com/1708683/43477154-426d849e-951c-11e8-8682-ac1950114a5a.png)

![generator](https://user-images.githubusercontent.com/1708683/43477174-5381d15e-951c-11e8-9f86-2e45acd38f08.png)

![settings](https://user-images.githubusercontent.com/1708683/43679408-67b724d0-9846-11e8-8eb0-49e04c449ee3.png)
 -->
## Author

[Olatunji Olayemi](http://cognitiveinc.xyz) :email: [Email Me](mailto:olayemi289@gmail.com)

## License

This project is licensed under the MIT License - see the [License File](LICENSE) for details
