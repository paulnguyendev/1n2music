<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\UserModel as MainModel;
use App\Models\PlanModel;
use App\Models\SubscriptionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class AIPlanController extends Controller{
    private $prefix;
    private $pathViewController;
    private $controllerName;
    private $model;
    private $planModel;
    private $subscriptionModel;
    public function __construct()
    {
        $this->model = new MainModel();
        $this->planModel = new PlanModel();
        $this->subscriptionModel = new SubscriptionModel();

        $this->prefix = rrt_get_config_by('core', 'prefix', 'admin');
        $this->controllerName = "{$this->prefix}/aiPlans";
        $this->pathViewController = "{$this->prefix}.pages.aiPlans";

        View::share('controllerName', $this->controllerName);
        View::share('prefix', $this->prefix);
        View::share('pathViewController', $this->pathViewController);
    }
    public function index(Request $request)
    {

        return view(
            "{$this->pathViewController}/index",
            []
        );
    }
    public function list(Request $request){
        $result = [];
        $draw = $request->draw ? $request->draw : 1;
        $start = $request->start ? $request->start : 0;
        $length = $request->length ? $request->length : 0;
        $search = $request->search ? $request->search : [];
        $searchValue = $search['value'] ?? "";
        $data = [];
        $params = [];
        $params['start'] = $start;
        $params['length'] = $length;
        $params['is_map'] = '1';
        $params['with'] = '1';
        $params['controllerName'] = $this->controllerName;
        $proSellerPlan = $this->planModel->find(3) ?? [];
        $subscriptionPlan = $this->subscriptionModel->orderBy('id', 'ASC')
            ->get()
            ->toArray();
        $plans = array_merge([$proSellerPlan], $subscriptionPlan);
        $data = array_replace([1 => $plans[3], 2 => $plans[0], 3 => $plans[2], 4 => $plans[1]]);
        $data = array_values($data);
        if ($searchValue) {
            $params['search'] = $searchValue;
            $filteredData = array_filter($data, function($item) use ($searchValue) {
                return strpos($item['name'], $searchValue) !== false; // Kiểm tra nếu tên chứa giá trị tìm kiếm
            });
            $data = array_values($filteredData);
        }
        $recordsTotal = count($data);
        $result = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data,
        ];
        return $result;
    }
    public function form(Request $request)
    {

        $slug = $request->slug;
        $type = $request->type;
        $item = [];
        $title = "Create Subscription";
        if ($slug && $type) {
            if ($slug == "pro") {
                $item = $this->planModel->whereType($type)->whereSlug($slug)->first();
            }else{
                $item = $this->subscriptionModel->whereType($type)->whereSlug($slug)->first();
            }
            $title = "Update Subscription";
        }else{
            Session::flash('error-plan','Does not exist subscription!');
            return back();
        }
        return view(
            "{$this->pathViewController}/form",
            [
                'title' => $title,
                'item' => $item,
            ]
        );
    }
    public function save(Request $request)
    {
        $params = $request->all();
        $slug = $request->slug;
        $item = [];
        if (!$slug) {
            $params['redirect'] = rrt_route($this->controllerName . "/index");
        } else {
            if ($slug == "pro") {
                $item = $this->planModel->whereSlug($slug)->first();
                $params['pricing_monthly'] = $params['price'] ?? 0;
            }else{
                $item = $this->subscriptionModel->whereSlug($slug)->first();
            }
        }
        $status = null;
        $error = [];
        $fields = [
            'name' => [
                'required' => 1,
            ],
            'content' => [
                'required' => 1,
            ]
        ];
        foreach ($fields as $field => $fieldItem) {
            $fieldValue = $params[$field] ?? "";
            $fieldName = ucfirst(str_replace("_", " ", $field));
            $fieldIsRequired = $fieldItem['required'] ?? 0;
            if ($fieldIsRequired == 1 && !$fieldValue) {
                $error[$field] = "Please enter {$fieldName}";
            }
        }
        if (empty($error)) {
            $params['pricing_annually'] = !empty($params['pricing_annually']) ? $params['pricing_annually'] : 0;
            $item->update($params);
            return $params;
        } else {
            return response()->json(
                $error,
                422,
            );
        }
    }
    public function delete(Request $request)
    {
        $data = $request->all();
        $slug = $request->slug;
        if ($slug == "pro") {
            $item = $this->planModel->whereType($data['type'])->whereSlug($slug)->first();
        }else{
            $item = $this->subscriptionModel->whereType($data['type'])->whereSlug($slug)->first();
        }
        if ($item) {
            $item->delete();
            return [
                'success' => true,
                'message' => 'Plan moved to trash',
            ];
        }else{
            return [
                'success' => false,
                'message' => 'Plan moved to trash fail!',
            ];
        }
    }
}
