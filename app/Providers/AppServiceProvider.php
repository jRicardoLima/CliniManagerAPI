<?php

namespace App\Providers;

use App\Repository\MediatorRepository\DispatchNotifier;
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
