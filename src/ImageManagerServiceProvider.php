<?php

namespace Elsayednofal\Imagemanager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;

class ImageManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config'=> config_path(),
            __DIR__.'/resources/views'=>resource_path('Views/backend/image-manager'),
            __DIR__ . '/assets' => public_path('vendor/elsayednofal/image-manager'),
             __DIR__. '/resources/langs'=>resource_path('lang/vendor/image-manager')
            ]);
        
        $this->loadTranslationsFrom(resource_path('lang/vendor/image-manager'), 'media-manager');
        //$this->loadTranslationsFrom(__DIR__.'lang/vendor/image-manager', 'media-manager');

        
        if(floatval(Application::VERSION) >= 5.3){
            $this->loadMigrationsFrom(__DIR__.'/migrations');
        }else{
            $this->publishes([__DIR__ . '/migrations' => database_path('migrations')]);
        }
        
        include __DIR__.'/routes.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
