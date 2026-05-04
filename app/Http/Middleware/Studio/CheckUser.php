<?php
namespace App\Http\Middleware\Studio;
use Closure;
use App\Models\SubscriptionOrderModel;
use App\Models\UserModel;
use App\Models\TrackModel;
class CheckUser
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
        $code = $request->code;
        $userID = rrt_get_user_login('id');
        $model = new UserModel();
        $user = $model::find($userID);
        $joinType = $user['join_type'] ?? "basic";
        if($joinType != 'basic') {
            return $next($request);
        }
        $trackModel = new TrackModel();
        $trackItem = $trackModel->getItem(['code' => $code], ['task' => 'code']);
        $subscriptionOrder = $user->subscriptionOrders()->first();
        $maxTrack = $subscriptionOrder->subscription->max_track ?? 0;
        $totalTrack = $user->tracks()->count()  ?? 0;
        $routeRedirect = rrt_get_config_by("session", $this->prefix, 'redirect');
        $session = rrt_get_config_by("session", $this->prefix, 'session');
        if ( empty($trackItem) &&  $totalTrack >= $maxTrack  && $maxTrack > 0 ) {
            return redirect()->route('public/studio/content/index', ['locale' => rrt_get_locale()])->with('warning', 'Maximum number of uploaded beats is 5 (Please upgrade to Pro for more)' );;
        }
        return $next($request);
    }
}
