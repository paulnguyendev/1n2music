<?php

namespace App\Providers;

use App\Http\View\Composers\GlobalComposer;
use App\Models\SettingModel;
use App\Models\NoticeModel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Alaouy\Youtube\Facades\Youtube;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $footer = SettingModel::get()->pluck('meta_value', 'meta_key')->toArray();
        Paginator::useBootstrap();
        View::share('footer', $footer);
        View::composer('*', GlobalComposer::class);
        Youtube::setApiKey(env('YOUTUBE_API_KEY'));
    }
}
