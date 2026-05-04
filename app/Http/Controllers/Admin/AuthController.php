<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Models\AdminModel as MainModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
#Mail

#Helper
class AuthController extends Controller
{
    private $prefix = "admin";
    private $pathViewController = "admin.pages.auth";
    private $controllerName = "admin/auth";
    private $model;
    private $params = [];
    public function __construct()
    {
        $this->model = new MainModel();
        View::share('pathViewController', $this->pathViewController);
        View::share('controllerName', $this->controllerName);
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
            []
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
        $routeLogin = rrt_get_config_by('session', $this->prefix, 'login');
        $session = rrt_get_config_by('session', $this->prefix, 'session');
        $request->session()->forget($session);
        return redirect(rrt_route($routeLogin));
    }
    public function postLogin(Request $request)
    {
        $params = $request->all();
        $password = $params['password'] ?? "";
        $params['password'] = rrt_encrypt_password($password);
        $user = $this->model->getItem($params, ['task' => 'login']);
        $status = null;
        $error = [];
        $msg = null;
        $redirect = null;
        $session = null;
        $redirectUrl = null;
        if (!$user) {
            $error['user'] = "Incorrect account or password";
        }
        elseif ($user['status'] != 'active') {
            $error['user'] = "Your account cannot access the system";
        }
        $sessionInfo = null;
        if (empty($error)) {
            $status = 200;
            $msg = "Logged in successfully";
            $redirect = rrt_get_config_by('session', $this->prefix, 'redirect');
            $session = rrt_get_config_by('session', $this->prefix, 'session');
            $redirectUrl = rrt_route($redirect);
            session()->push($session, $user);
            $sessionInfo = session()->all();
        } else {
            $status = 400;
            $msg = $error;
        }
        $params['user'] = $user;
        $params['status'] = $status;
        $params['msg'] = $msg;
        $params['redirectUrl'] = $redirectUrl;
        $params['session'] = $session;
        $params['sessionInfo'] = $sessionInfo;
        return $params;
    }
}
