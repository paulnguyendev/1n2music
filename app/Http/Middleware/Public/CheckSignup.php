<?php
namespace App\Http\Middleware\Public;
use Closure;
use App\Models\UserModel;
class CheckSignup
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
        $email = $request->session()->get('email', '');
        $model = new UserModel();
        $user = $model->getItem(['email' => $email],['task' => 'email']);
        $status = $user['status'] ?? "pending";
        if (!$user) {
            return $next($request);
        }
        if($status == 'pending') {
            return redirect()->route("public/join/basic/verifyCode", ['locale' => rrt_get_locale()]);
        }
        else {
            return redirect()->route("public/join/basic/signin", ['locale' => rrt_get_locale()]);
        }
        
    }
}
