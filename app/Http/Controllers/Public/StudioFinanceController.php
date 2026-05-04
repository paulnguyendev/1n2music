<?php
namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
#Model
use App\Models\UserModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
#Mail
use Illuminate\Support\Facades\Mail;
#Helper
use App\Models\PaymentAccountModel;
use App\Models\PayoutMethodInfoModel;
use App\Models\PayoutMethodModel;
use App\Models\TaxModel;
class StudioFinanceController extends Controller
{
    private $pathViewController = "studio.pages.finances";
    private $controllerName = "public/studio/finances";
    private $model;
    private $paymentMethodModel;
    private $params = [];
    private $prefix;
    private $payment_method_model;
    private $payment_account_model;
    private $payment_method_info_model;
    private $userModel;
    function __construct()
    {
        $this->prefix = rrt_get_config_by('core', 'prefix', 'studio');
        // $this->model = new MainModel();
        View::share('controllerName', $this->controllerName);
        View::share('pathViewController', $this->pathViewController);
        $this->payment_account_model = new PaymentAccountModel();
        $this->payment_method_info_model = new PayoutMethodInfoModel();
        $this->payment_method_model = new PayoutMethodModel();
        $this->userModel = new UserModel();
    }
    public function index(Request $request)
    {
        $user_id = rrt_get_user_login('id') ?? 0;
        $params['user_id'] = $user_id;
        $user = $this->userModel->where('id', $user_id)->with('paymentAccount')->first();
        return view(
            "{$this->pathViewController}/index",
            [
                'user' => $user
            ]
        );
    }
    public function form(Request $request)
    {
        $canEdit = $request->can_edit;
        $method = $request->input('method');
        $step = $request->step;
        $user_id = rrt_get_user_login('id') ?? 0;
        $params['user_id'] = $user_id;
        $user = $this->userModel->where('id', $user_id)->with('paymentAccount')->first();
        if ($step == 'general') {
            $tax = TaxModel::get()->toArray();
            $data = $user->paymentAccount ? $user->paymentAccount->toArray() : [];

        }

        if($data && !$canEdit) {
            return redirect()->route($this->controllerName . '/account', ['locale' => rrt_get_locale(), 'method' => 'paypal']);
        }
        return view(
            "{$this->pathViewController}/form",
            [
                'step' => $step,
                'data' => $data ?? [],
                'data_paypal_info' => $data_paypal_info ?? [],
                'data_bank_info' => $data_bank_info ?? [],
                'selected' => $selected ?? '',
                'method' => $method ?? [],
                'tax' => $tax ?? []
            ]
        );
    }
    public function postform(Request $request)
    {
        $request->validate([
            // 'business_name' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'date_of_birth' => 'required',
            'country' => 'required',
            'address_1' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
        ], [
            // 'business_name.required' => 'Business name not empty',
            'first_name.required' => __('First name not empty'),
            'last_name.required' => __('Last name not empty'),
            'email.required' => __('Email not empty'),
            'date_of_birth.required' => __('Date of Birth not empty'),
            'country.required' => __('Country not empty'),
            'address_1.required' => __('Address not empty'),
            'city.required' => __('City name not empty'),
            'postal_code.required' => __('Postal code not empty'),
            'tax_id' => __('Account Type not empty'),
        ]);
        $params = $request->all();
        $sesion_user_info = rrt_get_user_login();
        $params['user_id'] = $sesion_user_info['id'] ?? 1;
        $task = $params['id'] ? 'edit-item' : 'add-item';
        $method = $params['method'] ?? "";
        $this->payment_account_model->saveItem($params, ['task' => $task]);
        if ($method) {
            return redirect()->route($this->controllerName . '/account', ['locale' => rrt_get_locale(), 'method' => $method]);
        } else {
            return redirect()->route($this->controllerName . '/account', ['locale' => rrt_get_locale()]);
        }
    }
    public function account(Request $request)
    {
        $method = $request->method ?? 'paypal';
        $routeMethod = rrt_route($this->controllerName . "/account", ['method' => $method]);
        $user_id = rrt_get_user_login('id') ?? 0;
        $user = $this->userModel->where('id', $user_id)->with('paymentAccount')->first();
        $data = $user->paymentAccount ? $user->paymentAccount->toArray() : [];
        if (!$data) {
            return redirect()->route($this->controllerName . '/form', ['step' => 'general', 'locale' => rrt_get_locale()]);
        }
        $paymentAccount = $user ? $user->paymentAccount : [];
        $paymentAccountID = $paymentAccount ? $paymentAccount['id'] : '';
        $paymentMethod = $this->payment_method_model->getItem(['method' => $method, 'payout_account_id' => $paymentAccountID], ['task' => 'method']);
        $paymentMethodInfo = $paymentMethod ? $paymentMethod->info()->first() : [];
        return view(
            "{$this->pathViewController}/account",
            [
                'data' => $data,
                'method' => $method,
                'routeMethod' => $routeMethod,
                'paymentAccountID' => $paymentAccountID,
                'paymentMethodInfo' => $paymentMethodInfo,
                'paymentMethod' => $paymentMethod,
            ]
        );
    }
    public function postformPaypal(Request $request)
    {
        $data = $request->all();
        $user_id = rrt_get_user_login('id') ?? 0;
        $params['user_id'] = $user_id;
        $user = $this->userModel->where('id', $user_id)->with('paymentAccount')->first();
        $paymentAccount = $user->paymentAccount ? $user->paymentAccount : [];
        if (!$paymentAccount) {
            return redirect()->route($this->controllerName . '/account', ['method' => 'paypal', 'locale' => rrt_get_locale()]);
        }
        $paymentAccountID = $paymentAccount ? $paymentAccount['id'] : '';
        $paymentMethod = $this->payment_method_model->getItem(['method' => 'paypal', 'payout_account_id' => $paymentAccountID], ['task' => 'method']);
        $paymentMethodID = null;
        $paymentMethodInfo = null;
        $isActive = $data['is_active'] ?? 0;
        if (!$paymentMethod) {
            $paymentMethodID = $this->payment_method_model->saveItem(['payout_account_id' => $paymentAccountID, 'method' => 'paypal', 'is_active' => $isActive], ['task' => 'add-item']);
            if ($paymentMethodID) {
                $data['payout_method_id'] = $paymentMethodID;
                $this->payment_method_info_model->saveItem($data, ['task' => 'add-item']);
            }
        } else {
            $paymentMethodID = $paymentMethod['id'] ?? "";
            $paymentMethodInfo = $paymentMethod->info()->first();
            $data['id'] = $paymentMethodInfo['id'] ?? "";
            $data['payout_method_id'] = $paymentMethodID;
            $paymentMethodID = $this->payment_method_model->saveItem(['id' => $paymentMethodID, 'payout_account_id' => $paymentAccountID, 'method' => 'paypal', 'is_active' => $isActive], ['task' => 'edit-item']);
            $this->payment_method_info_model->saveItem($data, ['task' => 'edit-item']);
        }
        // $paymentMethodID = $paymentMethod['id'] ?? "";
        return redirect()->back()->with(['status' => 'success']);
        if ($paymentMethod) {
            $data['payout_method_info_id'] = $paymentMethod['id'] ?? "";
            $this->payment_method_info_model->updatePaypal($data);
        } else {
            $this->payment_method_info_model->createPaypal($data, $data['payout_method_id']);
        }
    }
    public function postformBank(Request $request)
    {
        $data = $request->all();

        $user_id = rrt_get_user_login('id') ?? 0;
        $params['user_id'] = $user_id;
        $user = $this->userModel->where('id', $user_id)->with('paymentAccount')->first();
        $paymentAccount = $user->paymentAccount ? $user->paymentAccount : [];
        if (!$paymentAccount) {
            return redirect()->route($this->controllerName . '/account', ['method' => 'bank', 'locale' => rrt_get_locale()]);
        }
        $paymentAccountID = $paymentAccount ? $paymentAccount['id'] : '';
        $paymentMethod = $this->payment_method_model->getItem(['method' => 'bank', 'payout_account_id' => $paymentAccountID], ['task' => 'method']);
        $paymentMethodID = null;
        $paymentMethodInfo = null;
        $isActive = $data['is_active'] ?? 0;
        if (!$paymentMethod) {
            $paymentMethodID = $this->payment_method_model->saveItem(['payout_account_id' => $paymentAccountID, 'method' => 'bank', 'is_active' => $isActive], ['task' => 'add-item']);
            if ($paymentMethodID) {
                $data['payout_method_id'] = $paymentMethodID;
                $this->payment_method_info_model->saveItem($data, ['task' => 'add-item']);
            }
        } else {
            $paymentMethodID = $paymentMethod['id'] ?? "";
            $paymentMethodInfo = $paymentMethod->info()->first();
            $data['id'] = $paymentMethodInfo['id'] ?? "";
            $data['payout_method_id'] = $paymentMethodID;
            $paymentMethodID = $this->payment_method_model->saveItem(['id' => $paymentMethodID, 'payout_account_id' => $paymentAccountID, 'method' => 'bank', 'is_active' => $isActive], ['task' => 'edit-item']);
            $this->payment_method_info_model->saveItem($data, ['task' => 'edit-item']);
        }
        // $paymentMethodID = $paymentMethod['id'] ?? "";
        return redirect()->back()->with(['status' => 'success']);
        if ($paymentMethod) {
            $data['payout_method_info_id'] = $paymentMethod['id'] ?? "";
            $this->payment_method_info_model->updateBank($data);
        } else {
            $this->payment_method_info_model->createBank($data, $data['payout_method_id']);
        }
    }
    // public function postformBank(Request $request)
    // {
    //     $data = $request->all();
    //     // $request->validate(
    //     //     [
    //     //         'bank_paymemt_number' => 'required',
    //     //         'bank_name_holder_card' => 'required',
    //     //         'bank_paymemt_city' => 'required',
    //     //         'bank_paymemt_address' => 'required',
    //     //         'bank_swift_bic' => 'required',
    //     //         'bank_paymemt_province' => 'required'
    //     //     ],
    //     //     [
    //     //         'bank_paymemt_number.required' => 'Number payment not empty',
    //     //         'bank_name_holder_card.required' => 'Name holder not empty',
    //     //         'bank_paymemt_city.required' => 'City not empty',
    //     //         'bank_paymemt_address.required' => 'Address payment not empty',
    //     //         'bank_swift_bic.required' => 'Swift not empty',
    //     //         'bank_paymemt_province.required' => 'Province not empty',
    //     //     ]
    //     // );
    //     $data = $request->all();
    //     $user_id = rrt_get_user_login('id') ?? 0;
    //     $params['user_id'] = $user_id;
    //     $user = $this->userModel->where('id', $user_id)->with('paymentAccount')->first();
    //     if (isset($user->paymentAccount)) {
    //         $method =  $user->paymentAccount->paymentMethod()
    //             ->where('method', 'bank')->first();
    //         $user->paymentAccount->paymentMethod()->where('method', 'paypal')->update(['is_selected' => 0]);
    //         $data['payout_method_id'] =  $method->id;
    //         if (isset($method->info)) {
    //             $data['payout_method_info_id'] = $method->info->id;
    //             $this->payment_method_info_model->updateBank($data);
    //         } else {
    //             $this->payment_method_info_model->createBank($data, $data['payout_method_id']);
    //         }
    //     }
    //     return redirect()->back()->with('status', 'success')->with('form', 'bank');
    // }
    public function activeMethod(Request $request)
    {
        $params['id'] = $request->id ?? '';
        $params['selected'] = 1;
        //   dd($request->all());
        $params['method'] = $request->method ?? '';
        //   dd($params);
        $params['user_id'] = rrt_get_user_login('id');
        $user = $this->userModel->getItem($params, ['task' => 'id']);
        $params['payout_account_id'] = $user->paymentAccount ? $user->paymentAccount->id : 0;
        $result = $this->payment_method_model->activeMethod($params);
        if ($result) {
            $redirect = rrt_route($this->controllerName . '/form', ['step' => 'bank']);
            $status_code = 200;
        } else {
            $status_code = 500;
        }
        return response()->json(['status_code' => $status_code, 'redirect' => $redirect ?? '', 'result' => $result]);
    }
}
