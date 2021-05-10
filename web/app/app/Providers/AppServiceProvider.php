<?php

namespace App\Providers;

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
        $this->app->singleton(
            \App\Repositories\TMM_User\TMM_UserRepositoryInterface::class,
            \App\Repositories\TMM_User\TMM_UserRepository::class
        );
        $this->app->singleton(
            \App\Repositories\UsageType\UsageTypeRepositoryInterface::class,
            \App\Repositories\UsageType\UsageTypeRepository::class
        );
        $this->app->singleton(
            \App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface::class,
            \App\Repositories\PaymentMethod\PaymentMethodRepository::class
        );
        $this->app->singleton(
            \App\Repositories\DailyUsage\DailyUsageRepositoryInterface::class,
            \App\Repositories\DailyUsage\DailyUsageRepository::class
        );
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
