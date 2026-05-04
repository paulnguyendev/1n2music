<?php

namespace App\Http\Middleware\Studio;

use Closure;
use Illuminate\Support\Facades\View;
use App\Models\NoticeModel;

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
        $this->prefix = rrt_get_config_by('core', 'prefix', 'studio');
    }
    public function handle($request, Closure $next)
    {
        $session = rrt_get_config_by("session", $this->prefix, 'session');
        $routeLogin = rrt_get_config_by("session", $this->prefix, 'login');
        if ($request->session()->has($session)) {

            #_Get User Info
            $user =  rrt_get_login_info($this->prefix);
            $noticeModel = new NoticeModel;
            $totalNotice = NoticeModel::where('admin_id', $user['id'] ?? '')->count();
            $notices = $noticeModel->listItems([], ['task' => 'dashboard']);
           
            View::share('notices', $notices);
            View::share('totalNotice', $totalNotice);
            View::share('userId', $user['id'] ?? '');

            if (!$user) {
                $request->session()->forget($session);
            } else {
                return $next($request);
            }
        }
        return redirect()->route($routeLogin, ['locale' => rrt_get_locale()]);
    }
}
