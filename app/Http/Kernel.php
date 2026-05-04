<?php

namespace App\Http;

use App\Http\Middleware\Studio\CheckAIUsageCount;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'studio.checkLogin' => \App\Http\Middleware\Studio\CheckLogin::class,
        'studio.checkAccess' => \App\Http\Middleware\Studio\CheckAccess::class,
        'public.CheckEmail' => \App\Http\Middleware\Public\CheckEmail::class,
        'public.CheckSignup' => \App\Http\Middleware\Public\CheckSignup::class,
        'public.CheckSignin' => \App\Http\Middleware\Public\CheckSignin::class,
        'user.checkLogin' => \App\Http\Middleware\User\CheckLogin::class,
        'user.checkAccess' => \App\Http\Middleware\User\CheckAccess::class,
        'admin.checkLogin' => \App\Http\Middleware\Admin\CheckLogin::class,
        'admin.checkAccess' => \App\Http\Middleware\Admin\CheckAccess::class,
        'setLocale' => \App\Http\Middleware\SetLocale::class,
        'public.CheckSigninAjax' =>  \App\Http\Middleware\Public\CheckSigninAjax::class,
        'public.CheckLogin' =>   \App\Http\Middleware\Public\CheckLogin::class,
        'studio.CheckUser' =>   \App\Http\Middleware\Studio\CheckUser::class,
        'studio.CheckDistribute' => \App\Http\Middleware\Studio\CheckDistribute::class,
        'public.CheckThreadView' => \App\Http\Middleware\Public\CheckThreadView::class,
        'checkAiUsageCount'=> CheckAIUsageCount::class,
        'maintenance' => \App\Http\Middleware\Maintenance::class,
        'studio.CheckStatus' => \App\Http\Middleware\CheckStatus::class,
    ];
}
