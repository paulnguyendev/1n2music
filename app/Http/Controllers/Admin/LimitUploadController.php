<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#Model
use App\Models\SettingModel;
use Illuminate\Http\Request;
#Mail
use Illuminate\Support\Facades\View;

class LimitUploadController extends Controller
{
    private $prefix;
    private $model;
    private $pathViewController;
    private $controllerName;
    public function __construct()
    {
        $this->model = new SettingModel;
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/limitUpload";
        $this->pathViewController = "{$this->prefix}.pages.limitUpload";
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request){
        $settingSingle = $this->model->whereMeta_key('limit_single_upload')->first();
        $settingAlbum = $this->model->whereMeta_key('limit_album_upload')->first();
        $params = [
            'limit_single' => $settingSingle ? $settingSingle->meta_value : 3,
            'limit_album' => $settingAlbum ? $settingAlbum->meta_value : -1,
        ];
        return view ($this->pathViewController.'.index',$params);
    }
    public function save(Request $request){
        
        // Validate
        $params = $request->all();
        $error = [];
        $fields = [
            'limit_single' => [
                'required' => 1,
                'accpect' => 1,
            ],
            'limit_album' => [
                'required' => 1,
                'accpect' => 1,
            ]
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
        $settingUpload = [
            'limit_single_upload' => $request->limit_single??3,
            'limit_album_upload' => $request->limit_album??-1,
        ];
        foreach ($settingUpload as $key => $value) {
            $settingsModel->saveItem([$key => ($value)]);
        }

        return response()->json(['status' => 'success', 'message' => 'Limit upload updated successfully!']);
    }
}
