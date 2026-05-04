<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
#Model
// use App\Models\ProductModel as MainModel;
use App\Models\UserModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class AuthController extends Controller
{
    private $prefix;
    private $pathViewController     = "studio.pages.auth";
    private $controllerName         = "studio/auth";
    private $model;
    private $params                 = [];
    function __construct()
    {
        // $this->model = new MainModel();
        $this->prefix = rrt_get_config_by('core','prefix','studio');
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        
    }
    public function index(Request $request)
    {
        return view(
            "{$this->pathViewController}/index",
            []
        );
    }
    public function login(Request $request)
    {
       
        return view(
            "{$this->pathViewController}/login",
            [
               
            ]
        );
    }
    public function register(Request $request)
    {
        return view(
            "{$this->pathViewController}/register",
            []
        );
    }
    public function logout(Request $request)
    {
        $routeLogin = rrt_get_config_by('session',$this->prefix,'login');
        $session = rrt_get_config_by('session',$this->prefix, 'session');
        $request->session()->forget($session);
        return redirect(route($routeLogin));
    }
}
