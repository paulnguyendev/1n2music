<?php
use Illuminate\Support\Facades\Route;
$prefix = rrt_get_config_by('core','prefix','user');
Route::middleware('user.checkAccess')->group(function () use($prefix) {
    $routeName = "{$prefix}/home";
    Route::controller(HomeController::class)->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
    });
   
});
Route::prefix('auth')->group(function () use ($prefix) {
    $routeName = "{$prefix}/auth";
    Route::controller(AuthController::class)->group(function () use ($routeName) {
        Route::get('/login', 'login')->name($routeName . '/login')->middleware('user.checkLogin');
        Route::get('/register', 'register')->name($routeName . '/register');
        Route::get('/logout', 'logout')->name($routeName . '/logout');
    });
});