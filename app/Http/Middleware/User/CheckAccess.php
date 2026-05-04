<?php
namespace App\Http\Middleware\User;
use Closure;
class CheckAccess
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
        $this->prefix = rrt_get_config_by('core','prefix','user');
    }
    public function handle($request, Closure $next)
    {
        $session = rrt_get_config_by("session", $this->prefix, 'session');
        $routeLogin = rrt_get_config_by("session", $this->prefix, 'login');
        if ($request->session()->has($session)) {
            #_Get User Info
            $user =  rrt_get_login_info($this->prefix);
            if (!$user) {
                $request->session()->forget($session);
            } else {
                return $next($request);
            }
        }
        return redirect()->route($routeLogin);
    }
}
