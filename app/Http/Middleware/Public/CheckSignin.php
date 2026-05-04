<?php

namespace App\Http\Middleware\Public;

use Closure;
use App\Models\UserModel;

class CheckSignin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $prefix;
    function __construct()
    {
        $this->prefix = rrt_get_config_by('core', 'prefix', 'studio');
    }
    public function handle($request, Closure $next)
    {
        $routeRedirect = rrt_get_config_by("session", $this->prefix, 'redirect');
        $session = rrt_get_config_by("session", $this->prefix, 'session');

        if ($request->session()->has($session)) {
            return redirect()->route($routeRedirect, ['locale' => rrt_get_locale()]);
        }
        return $next($request);
    }
}
