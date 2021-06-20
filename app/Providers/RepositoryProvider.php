<?php

namespace App\Providers;

use App\Repository\IRepository;
use App\Repository\RepositoryFactory;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RepositoryFactory::class,function($app,$class){
            $factory = new $class['class']();
            return (new RepositoryFactory($factory->createFactory()))->factory();
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
