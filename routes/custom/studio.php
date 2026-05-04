<?php

use Illuminate\Support\Facades\Route;

$prefix = rrt_get_config_by('core', 'prefix', 'studio');
Route::prefix('{locale}')->middleware(['setLocale', 'studio.checkAccess'])->group(function () use ($prefix) {
    $routeName = "{$prefix}/home";
    Route::controller(HomeController::class)->group(function () use ($routeName) {
        Route::get('/', 'index')->name($routeName . '/index');
    });
});
Route::prefix('auth')->group(function () use ($prefix) {
    $routeName = "{$prefix}/auth";
    Route::controller(AuthController::class)->group(function () use ($routeName) {
        Route::get('/login', 'login')->name($routeName . '/login')->middleware('studio.checkLogin');
        Route::get('/register', 'register')->name($routeName . '/register');
        Route::get('/logout', 'logout')->name($routeName . '/logout');
    });
});
