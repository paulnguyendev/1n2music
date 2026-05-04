<?php
namespace App\Http\Middleware\Studio;
use App\Models\UserModel;
use Closure;
class CheckAIUsageCount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @param  int  $ai_id
     */
    private $prefix;
    function __construct()
    {

        $this->prefix = rrt_get_config_by('core', 'prefix', 'studio');
    }
    public function handle($request, Closure $next, $ai_id)
    {
        $userId =rrt_get_user_login('id');
        $user = UserModel::find($userId);
        $userAIUsageCount = $user->ai_usage_count??0;
        if ($userAIUsageCount <= 0){
            $packageRole = rrt_get_package_with_role();
            $checkoutUrl = null;
            if ($packageRole && isset($packageRole['packages'])) {
                $package = $packageRole['packages']->where('pivot.ai_id', $ai_id)->first();
                if ($package) {
                    $checkoutUrl = rrt_route('public/studio/orderAi/checkout', [
                        'package_id' => $package->pivot->package_id??"",
                        'role_id' => $packageRole['role']->id??""
                    ]);
                }
            }
            if ($checkoutUrl){
                return redirect($checkoutUrl);
            }
            else{
                return redirect()->back();
            }
        }
        return $next($request);
    }
}
