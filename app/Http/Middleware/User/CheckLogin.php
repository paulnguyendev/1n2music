<?php

namespace App\Http\Middleware\User;

use Closure;

class CheckLogin
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
        $this->prefix = rrt_get_config_by('core', 'prefix', 'user');
    }
    public function handle($request, Closure $next)
    {
        // dd(12);
        $routeRedirect = rrt_get_config_by("session", $this->prefix, 'redirect');
        //  dd($routeRedirect);
        $session = rrt_get_config_by("session", $this->prefix, 'session');
        if ($request->session()->has($session)) {
            return redirect()->route($routeRedirect);
        }
        // dd('vao');
        return $next($request);
    }
}
