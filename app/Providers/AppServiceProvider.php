<?php

namespace App\Providers;

use App\Repositories\Banks\BanksEloquentORM;
use App\Repositories\Banks\BanksRepositoryInterface;
use App\Repositories\Comune\ComuneEloquentORM;
use App\Repositories\Comune\ComuneRepositoryInterface;
use App\Repositories\Municipality\MunicipalityEloquentORM;
use App\Repositories\Municipality\MunicipalityRepositoryInterface;
use App\Repositories\Province\ProvinceEloquentORM;
use App\Repositories\Province\ProvinceRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
          $this->app->bind(
                 ProvinceRepositoryInterface::class, ProvinceEloquentORM::class
             );
             $this->app->bind(
                      MunicipalityRepositoryInterface::class, MunicipalityEloquentORM::class
                  );
                  $this->app->bind(
                           ComuneRepositoryInterface::class, ComuneEloquentORM::class
                       );
                  $this->app->bind(
                           BanksRepositoryInterface::class, BanksEloquentORM::class
                       );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
