<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
#Model
use App\Models\PlanModel as MainModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
class SellBeatController extends Controller
{
    private $pathViewController     = "public.pages.join.sellBeat";
    private $controllerName         = "public/join/sellBeats";
    private $model;
    private $params                 = [];
    function __construct()
    {
        $this->model = new MainModel();
        View::share('controllerName', $this->controllerName);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {
        $plans = $this->model->listItems(['order_number' => '1'], ['task' => 'list']);
        return view(
            "{$this->pathViewController}/index",
            [
                'plans' => $plans,
            ]
        );
    }
    public function register(Request $request)
    {
        $slug = $request->plan;
        $cycle = $request->cycle ?? "manual";
        $item = $this->model->getItem(['slug' => $slug], ['task' => 'slug']);
        $id = $item['id'] ?? "";
        return view(
            "{$this->pathViewController}/register",
            [
                'id' => $id,
                'cycle' => $cycle,
            ]
        );
    }
}
