<?php
namespace JingBh\AutoZP;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use JingBh\AutoZP\Http\Middleware\CheckInvite;

class AutoZPServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // $this->loadRoutesFrom(__DIR__ . "/Http/routes.php");
        $this->loadMigrationsFrom(__DIR__ . "/../database/migrations");
        $this->loadViewsFrom(__DIR__ . "/../resources/views", "autozp");

        Route::middlewareGroup("autozp", [CheckInvite::class]);
    }
}
