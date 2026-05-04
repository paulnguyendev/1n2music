<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
#Model
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\PlatformModel as MainModel;
#Helper
class PlatformController extends Controller
{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    public function __construct()
    {
        $this->model = new MainModel();
        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/platform";
        $this->pathViewController = "{$this->prefix}.pages.platform";
        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request){
        $platforms = $this->model->orderBy('updated_at', 'desc')->get();
        return view($this->pathViewController.'.index',[
            'platforms'=>$platforms
        ]);
    }
    public function form(Request $request){
        $id = $request->id;
        $item = [];
        $title = "Create a New Platform";

        if ($id) {
            $item = $this->model->getItem(['id' => $id, 'with' => '1'], ['task' => 'id']);

            $title = "Update Platform";
        }
        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,
                'item' => $item,
                'id' => $id,
            ]
        );
    }
    public function save(Request $request)
    {
        $params = $request->all();
        $id = $request->id;

        // Initialize variables
        $error = [];
        $fields = [
            'name' => [
                'required' => 1,
                'unique' => 1,
            ],
            'status' => [
                'required' => 1,
                'unique' => 0,
            ],
        ];

        // Validation check
        foreach ($fields as $field => $fieldItem) {
            $fieldValue = $params[$field] ?? "";
            $fieldName = ucfirst(str_replace("_", " ", $field));
            $fieldIsRequired = $fieldItem['required'] ?? 0;
            $fieldIsUnique = $fieldItem['unique'] ?? 0;

            if ($fieldIsRequired == 1 && !$fieldValue) {
                $error[$field] = "Please enter {$fieldName}";
            } elseif ($fieldIsUnique == 1) {
                $existingItem = $this->model->where($field, $fieldValue)->first();
                if ($existingItem && $existingItem->id != $id) {
                    $error[$field] = "{$fieldName} already exists";
                }
            }
        }

        if (empty($error)) {
            if (isset($params['settings'])) {
                $params['settings'] = json_encode($params['settings']);
            }
            if ($id) {
                $params['updated_at'] = date('Y-m-d H:i:s');
                unset($params['_token']);
                unset($params['data_attributes']);
                $this->model->where('id', $id)->update($params);
                $message = "Update successfully";
            } else {
                $params['created_at'] = date('Y-m-d H:i:s');
                $newItem = $this->model->create($params);
                $id = $newItem->id;
                $message = "Add successfully";
            }
            $params['redirect'] = rrt_route($this->controllerName.'/index');
            return response()->json($params);
        } else {
            return response()->json($error, 422);
        }
    }
}
