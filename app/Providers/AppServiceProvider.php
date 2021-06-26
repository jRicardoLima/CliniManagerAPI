<?php

namespace App\Providers;

use App\Repository\MediatorRepository\DispatchNotifier;
use App\Services\HttpClientService\IHttpClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DispatchNotifier::class,function($app,$repository){
            $notified = new $repository['classNotified'];
            $notifier = new $repository['classNotifier'];
            return new DispatchNotifier($notified,$notifier);
        });
        $this->app->bind(IHttpClient::class,function($app,$class){
            return new $class['className'];
        });
        $this->app->singleton(\Faker\Generator::class,function(){
            return \Faker\Factory::create('pt_BR');
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
