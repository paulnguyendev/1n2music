<?php

namespace App\Http\Middleware\Public;

use Closure;

class CheckEmail
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
    }
    public function handle($request, Closure $next)
    {

        if ($request->session()->has('email')) {
            return $next($request);
        }
        return redirect()->route("public/join/basic/index", ['locale' => rrt_get_locale()]);
    }
}
