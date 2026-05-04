<?php

namespace App\Http\Middleware\Studio;

use App\Helpers\Subscription;
use App\Models\UserModel;
use Closure;

class CheckDistribute
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

        $redirect = Subscription::checkSubscription([1, 2]);
        if ($redirect) {
            return redirect($redirect);
        }


        return $next($request);
    }
}
