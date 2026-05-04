<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Models\SettingModel;
class Maintenance
{
    private $model;
    /**
     * Handle an incoming request.
     *
     */
    function __construct()
    {
        $this->model = new SettingModel();
    }
    public function handle(Request $request, Closure $next){

        $settingsMaintenance = $this->model->whereMeta_key('maintenance_mode_on')->first();
        $value = $settingsMaintenance->meta_value ?? 0;
        if($value == 1){
            return Response::view('maintenance', [], 503);
        }
        return $next($request);
    }
}


