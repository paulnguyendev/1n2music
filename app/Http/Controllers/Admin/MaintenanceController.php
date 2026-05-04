<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Models\PlanModel;
use App\Models\PlanOrderModel;
use App\Models\ManagerFileModel as MainModel;
use App\Models\SettingModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
#Mail
use Illuminate\Support\Facades\View;

#Helper
class MaintenanceController extends Controller{
    private $prefix;
    private $model;
    private $pathViewController;
    private $controllerName;
    public function __construct()
    {
        $this->model = new SettingModel;
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/maintenance";
        $this->pathViewController = "{$this->prefix}.pages.maintenance";
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request){
        $settingsMaintenance = $this->model->whereMeta_key('maintenance_mode_on')->first();
        return view($this->pathViewController.'.index',['settingsMaintenance' => $settingsMaintenance]);
    }
    public function save(Request $request){
        $params = $request->all();
        $error = [];
        $fields = [
            'maintenance_mode_on' => [
                'required' => 1,
                'accpect' => 1,
            ],
        ];
        foreach ($fields as $field => $fieldItem) {
            $fieldValue = $params[$field] ?? "";
            $fieldName = ucfirst(str_replace("_", " ", $field));
            $fieldIsRequired = $fieldItem['required'] ?? 0;
            $fieldIsAccpect = $fieldItem['accpect'] ?? 0;
            if ($fieldIsRequired == 1 && ($fieldValue === "" || $fieldValue === null)) {
                $error[$field] = "Please enter {$fieldName}";
            }
            elseif ($fieldIsAccpect == 1) {
                if (!is_numeric($fieldValue) || intval($fieldValue) != $fieldValue || intval($fieldValue) < -1) {
                    $error[$field] = "{$fieldName} must be an integer and greater than or equal to -1.";
                }
            }
        }
        if ($error) {
            return response()->json(
                $error,
                422,
            );
        }

        $settingsModel = new SettingModel();
        $settingMaintenance = [
            'maintenance_mode_on' => $request->maintenance_mode_on??0,
        ];
        foreach ($settingMaintenance as $key => $value) {
            $settingsModel->saveItem([$key => ($value)]);
        }

        return response()->json(['status' => 'success', 'message' => 'Change mode successfully!']);
    }
}
