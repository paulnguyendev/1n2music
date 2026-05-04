<?php

namespace App\Providers;

use App\Models\PageModel;
use App\Models\SettingModel;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public $path = "routes/custom/";
    public $namepace = "App\Http\Controllers";
    public const HOME = '/home';
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
            #_Custom Route
            $this->customRoute();
        });
    }
    public function customRoute()
    {
        #_Public Route
        Route::middleware('web', 'setLocale','maintenance')
            ->namespace($this->namepace . '\Public')
            ->group(base_path($this->path . 'public.php'));
        #_Studio Route
        $prefix = rrt_get_config_by('core', 'prefix', 'studio');

        Route::middleware('web', 'setLocale','maintenance')
            ->prefix($prefix)
            ->namespace($this->namepace . '\Studio')
            ->group(base_path($this->path .  'studio.php'));
        #_Admin Route
        $prefix = rrt_get_config_by('core', 'prefix', 'admin');
        Route::middleware('web', 'setLocale')
            ->namespace($this->namepace .  '\Admin')
            ->prefix('{locale}/' . $prefix)
            ->group(base_path($this->path .  'admin.php'));
        #User Route
        $prefix = rrt_get_config_by('core', 'prefix', 'user');
        Route::middleware('web', 'setLocale','maintenance')
            ->namespace($this->namepace .  '\User')
            ->prefix($prefix)
            ->group(base_path($this->path .  'user.php'));
    }
    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
