<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Repositories\UserRepositoryInterface::class, \App\Repositories\UserRepository::class);
        $this->app->bind(\App\Repositories\StudentRepositoryInterface::class, \App\Repositories\StudentRepository::class);
        $this->app->bind(\App\Repositories\StudentReportRepositoryInterface::class, \App\Repositories\StudentReportRepository::class);
        $this->app->bind(\App\Repositories\ClassroomRepositoryInterface::class, \App\Repositories\ClassroomRepository::class);
        $this->app->bind(\App\Repositories\StudentReportRepositoryInterface::class, \App\Repositories\StudentReportRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
