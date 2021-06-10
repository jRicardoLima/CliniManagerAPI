<?php

namespace App\Providers;

use App\Models\FactoriesModels\ModelsFactory;
use Illuminate\Support\ServiceProvider;

class ModelFactoryProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ModelsFactory::class,function($app,$class){

           $factory = new $class['className'];
           return (new ModelsFactory($factory))->factory();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
