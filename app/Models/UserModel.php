<?php
namespace App\Models;
use App\Helpers\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;
class UserModel extends Model
{
    use NodeTrait, SoftDeletes;
    protected $table = 'rrt_users';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order'];
    protected $fillable = ['avatar', 'is_comment', 'bio','address','date_of_birth','sns_id','sns_platform','main_payment_method', 'is_homepage', 'id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email', 'phone', 'username', 'password', 'identification', 'thumbnail', 'ref_code', 'description', 'validate_code', 'bank_name', 'bank_number', 'bank_owner', 'ipi_cae', 'pro', 'tax_documents', 'token', 'parent_id', '_lft', '_rgt', 'status', 'role', 'created_at', 'updated_at', 'join_type', 'join_date', 'expiration_date', 'pro_organization', 'tax_type', 'location','ai_usage_count', 'currency', 'count_update_name', 'ai_usage_count_reconize','country_code','city','country','zip_code','accomplishments', 'work_history','discography','youtube_link'];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];
    use HasFactory;

    // Scope for filtering
    public function scopeSearch($query, $searchTerm)
    {
        if (!empty($searchTerm)) {
            return $query->where(function($q) use ($searchTerm) {
                $q->where('username', 'LIKE', '%'.$searchTerm.'%')
                  ->orWhere('email', 'LIKE', '%'.$searchTerm.'%');
            });
        }
        return $query;
    }
    public function scopeSort($query, $sortOrder)
    {
        if (!empty($sortOrder)) {
            $sortOrder = $sortOrder == 'asc' ? 'asc' : 'desc';
            return $query->orderBy('username', $sortOrder);
        }
        return $query->orderBy('id', 'desc');
    }
    public function getFullPhoneAttribute(){
        return '(+'.($this->country_code ?? '').') '.$this->phone??'';
    }

    public function listItems($params = "", $options = "")
    {
        $result = null;
        $query = $this->select($this->fillable);
        if (isset($params['role'])) {
            if ($params['role'] == 'user') {
                $query = $query->where('role', 'user');
                $query  =  $query->whereHas('subscriptionOrders', function ($query) use ($params) {
                    if (isset($params['account_type'])) {
                        if ($params['account_type'] == 'publishing') {
                            return  $query->where('rrt_subscription_orders.subscription_id', 1);
                        } elseif ($params['account_type'] == 'distribution') {
                            return  $query->where('rrt_subscription_orders.subscription_id', 2);
                        } else {
                            return  $query->where('rrt_subscription_orders.subscription_id', 3);
                        }
                    }
                });
            } else {
                $query = $query->where('role', 'seller');
            }
        }
                // if ($params['account_type'] == 'publishing') {
        // } elseif ($params['account_type'] == 'distribution') {
        //     $query  =  $query->whereHas('subscriptionOrders', function ($query) {
        //         return  $query->where('rrt_subscription_orders.subscription_id', 2);
        //     });
        // } else {
        //     $query = $query->where('role', 'seller');
        // }
        if ($options['task'] == 'admin') {
            if (isset($params['start'])) {
                $query = $query->skip($params['start']);
            }
            if (isset($params['length'])) {
                $query = $query->take($params['length']);
            }
            if (isset($params['search'])) {
                $result = $query->where('first_name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('last_name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('phone', 'LIKE', "%{$params['search']}%")
                    ->orWhere('username', 'LIKE', "%{$params['search']}%")
                    ->orWhere('email', 'LIKE', "%{$params['search']}%");
            }
            // if (isset($params['role'])) {
            //     $query = $query->where('role', $params['role']);
            // }
            if (isset($params['not_id'])) {
                $query = $query->where('id', '!=', $params['not_id']);
            }
            $result = $query->orderBy('id', 'desc')->with('taxtypes')->with('contracts')->get();
            //  dd($result);
            if (isset($params['is_map'])) {
                $result = $result->map(function ($item) use ($params) {
                    $id = $item->id;
                    $item->plan_info = "123";
                    $status = $item->status ?? "";
                    if ($status == 'active') {
                        $statusClass = 'success';
                    } elseif ($status == 'suspend') {
                        $statusClass = 'danger';
                    } else {
                        $statusClass = 'primary';
                    }
                    $statusName = ucfirst($status);
                    $item->status_name = $statusName;
                    $item->status_class = $statusClass;
                    #_Information
                    $fullname = $item->first_name . " " . $item->last_name;
                    $item->fullname = $item->first_name ? $fullname : "-";
                    $item->info = "
                    {$fullname}<br>
                    <small><span class = 'text-{$statusClass}'>{$statusName}<span></small>
                    ";
                    $item->join_types = rrt_get_user_joinType($item);
                    #_Route
                    $controllerName = $params['controllerName'] ?? "";
                    $account_type = $params['account_type'] ?? '';
                    $item->route_update = rrt_route($controllerName . "/update", ['id' => $id, 'account_type' => $account_type]);
                    $item->route_edit = rrt_route($controllerName . "/form", ['id' => $id, 'account_type' => $account_type]);
                    $item->route_remove = rrt_route($controllerName . "/delete", ['id' => $id, 'account_type' => $account_type]);
                    $item->route_list_payment = rrt_route($controllerName . '/listPayment', ['id' => $id]);
                    return $item;
                });
            }
            if (isset($params['count'])) {
                $result = $result->count();
            }
        }

        if ($options['task'] == 'all') {
            $result = $query->orderBy('id', 'desc')->get();
            if (isset($params['count'])) {
                $result = $result->count();
            }
        }
        if ($options['task'] == 'list') {
            if (isset($params['status'])) {
                $query = $query->where('status', $params['status']);
            }
            // if (isset($params['role'])) {
            //     $query = $query->where('role', $params['role']);
            // }
            if (isset($params['start']) && isset($params['length'])) {
                $result = $query->orderBy('id', 'desc')->skip($params['start'])->take($params['length'])->get();
            } else {
                if (isset($params['not_id'])) {
                    $query = $query->where('id', '!=', $params['not_id']);
                }
                $result = $query->orderBy('id', 'desc')->get();
            }
        }
        if ($options['task'] == 'ajax') {
            $result = $query->where('status', 'active')->where('is_homepage', 1)->orderBy('id', 'desc')->get();
        }
        if ($options['task'] == 'send-mail') {
            $query = $this->select($this->checkEmail);
            if ($params['user_type']){
                switch ($params['user_type']) {
                    case 'basic':
                        $query->where('role', 'user')
                            ->whereHas('subscriptionOrders', function ($subQuery) {
                                $subQuery->where('rrt_subscription_orders.subscription_id', 3);
                            });
                        break;
                    case 'seller':
                        $query->where('role', 'seller');
                        break;
                    case 'distribution':
                        $query->where('role', 'user')
                            ->whereHas('subscriptionOrders', function ($subQuery) {
                                $subQuery->where('rrt_subscription_orders.subscription_id', 2);
                            });
                        break;
                    case 'publishing':
                        $query->where('role', 'user')
                            ->whereHas('subscriptionOrders', function ($subQuery) {
                                $subQuery->where('rrt_subscription_orders.subscription_id', 1);
                            });
                        break;
                    default:
                        break;
                }
            }
            $result = $query->orderBy('id', 'desc')->get();
            if (isset($params['count'])) {
                $result = $result->count();
            }
        }
        if ($options['task'] == 'count-user-week-ago') {
            $currentDate = Carbon::now();
            $sevenDaysAgo = Carbon::now()->subDays(7);
            $count = 0;
            $register = [];
            for ($i = 0; $i < 7; $i++) {
                $date = $currentDate->copy()->subDays($i);
                $count_date = $this->whereDate('created_at', $date)->count();
                $register[$date->format('Y-m-d')] = $count_date;
                $count += $count_date;
            }
            $previousWeekCount = $this->whereBetween('created_at', [$sevenDaysAgo->startOfWeek(), $sevenDaysAgo->endOfWeek()])->count();
            $currentWeekCount = array_sum(array_slice($register, 0, 7));
            $count_previousWeekCount = $previousWeekCount == 0 ? 1 : $previousWeekCount;
            $growthPercentage = ($currentWeekCount - $previousWeekCount) / $count_previousWeekCount * 100;
            return [
                'count' => $count,
                'register' => $register,
                'growth_percentage' => $growthPercentage
            ];
        }
        if ($options['task'] == 'filterRole') {
            if (isset($params['search'])) {
                $query = $query->where('first_name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('last_name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('phone', 'LIKE', "%{$params['search']}%")
                    ->orWhere('username', 'LIKE', "%{$params['search']}%")
                    ->orWhere('email', 'LIKE', "%{$params['search']}%");
            }
            // Lấy dữ liệu với sắp xếp và quan hệ
            $result = $query->orderBy('id', 'desc')->with('taxtypes')->with('contracts')->get();
            // Lọc theo account_type nếu có
            if (isset($params['account_type']) && is_array($params['account_type'])) {
                $account_types = $params['account_type'] ?? ['free-user'];
                $result = $result->filter(function ($item) use ($account_types) {
//                    $itemRole = rrt_get_user_role($item);
                    $itemRole = rrt_get_all_user_roles($item);
                    return !empty(array_intersect($account_types, $itemRole));
                });
            }
            $result = $result->values();

            if (isset($params['is_map'])) {
                $type = $params['type'] ?? "free-user";
                $result = $result->map(function ($item) use ($params, $type) {
                    $id = $item->id;
                    $item->plan_info = "123";
                    $status = $item->status ?? "";
                    $item->status_class = match ($status) {
                        'active' => 'success',
                        'suspend' => 'danger',
                        default => 'primary',
                    };
                    $item->status_name = ucfirst($status);
                    $fullname = $item->first_name . " " . $item->last_name;
                    $item->fullname = $fullname ?: "-";
                    $item->info = "{$fullname}<br><small><span class='text-{$item->status_class}'>{$item->status_name}</span></small>";
                    $controllerName = $params['controllerName'] ?? "";
                    $account_type = $params['account_type'] ?? '';
                    $item->route_update = rrt_route("{$controllerName}/update", ['id' => $id, 'account_type' => $type]);
                    $item->route_edit = rrt_route("{$controllerName}/form", ['id' => $id, 'account_type' => $type]);
                    $item->route_remove = rrt_route("{$controllerName}/delete", ['id' => $id, 'account_type' => $type]);
                    $item->route_list_payment = rrt_route("{$controllerName}/listPayment", ['id' => $id]);

                    return $item;
                });
            }
            if (isset($params['start'])) {
                $result = $result->slice($params['start']);
            }
            if (isset($params['length'])) {
                $result = $result->slice(0, $params['length']);
            }
            $result = $result->values();
            // Nếu cần đếm số bản ghi
            if (isset($params['count'])) {
                return $result->count();
            }
        }
        return $result;
    }
    public function getItem($params = [], $options = [])
    {
        $result = null;
        if ($options['task'] == 'account') {
            $query = $this->select($this->checkEmail);
            $result = $query->where('username', $params['account'])->orWhere('email', $params['account'])->first();
        }
        $query = $this->select($this->fillable);
        if ($options['task'] == 'login') {
            $result = $query->where('username', $params['username'])->first();
        }
        if ($options['task'] == 'email') {
            $result = $query->where('email', $params['email'])->first();
        }
        if ($options['task'] == 'phone') {
            $result = $query->where('phone', $params['phone'])->first();
        }
        if ($options['task'] == 'username') {
            $result = $query->where('username', $params['username'])->first();
        }
        if ($options['task'] == 'id') {
            $result = $query->where('id', $params['user_id'])->first();
        }
        if ($options['task'] == 'user_id') {
            $result = $query->where('id', $params['id'])->first();
        }
        if ($options['task'] == 'token') {
            $result = $query->where('token', $params['token'])->first();
        }
        if ($options['task'] == 'identification') {
            $result = $query->where('identification', $params['identification'])->first();
        }
        if ($options['task'] == 'avatar') {
            $result = $query->where('identification', $params['identification'])->first();
        }
        if ($options['task'] == 'check') {
            if (isset($params['email'])) {
                $query = $query->where('email', $params['email']);
            }
            if (isset($params['phone'])) {
                $query = $query->where('phone', $params['phone']);
            }
            if (isset($params['username'])) {
                $query = $query->where('username', $params['username']);
            }
            $result = $query->first();
        }
        return $result;
    }
    public function saveItem($params = [], $option = [])
    {
        //dd($params);
        if ($option['task'] == 'add-item') {
            if (!isset($params['account_type'])) {
                $params['is_homepage'] = 0;
            }
            $paramsInsert = array_diff_key($params, array_flip($this->crudNotAccepted));
            $parent = self::find($params['parent_id']);
            $result =    self::create($paramsInsert, $parent);
            if (isset($params['account_type'])) {
                if ($params['account_type'] == 'publishing') {
                    $subscription_id = 1;
                } else if ($params['account_type'] == 'distribution') {
                    $subscription_id = 2;
                } elseif ($params['account_type'] == 'basic') {
                    $subscription_id = 3;
                }
                SubscriptionOrderModel::create([
                    'subscription_id' => $subscription_id,
                    'total' => 0,
                    'status' => 'pending',
                    'user_id' => $result->id,
                ]);
            } else {
                if (!isset($params['basic_signup'])) {
                    SubscriptionOrderModel::create([
                        'subscription_id' => 3,
                        'total' => 0,
                        'status' => 'pending',
                        'user_id' => $result->id,
                    ]);
                }
            }
            ContractSettingModel::create([
                'contract_id' => 4,
                'category' => 'regular',
                'status' => 'public',
                'user_id' => $result->id,
                'deliverables' => 'untaggedMp3Wave',
                'enabled' => 1,
            ]);
            ContractSettingModel::create([
                'contract_id' => 3,
                'category' => 'regular',
                'status' => 'public',
                'user_id' => $result->id,
                'deliverables' => 'unTaggedMp3',
                'enabled' => 1,
            ]);
            ContractSettingModel::create([
                'contract_id' => 5,
                'category' => 'regular',
                'status' => 'public',
                'user_id' => $result->id,
                'deliverables' => 'stems',
                'enabled' => 1,
            ]);
            ContractSettingModel::create([
                'contract_id' => 1,
                'category' => 'free',
                'status' => 'public',
                'user_id' => $result->id,
                'deliverables' => 'taggedMp3',
                'enabled' => 1,
            ]);
            return $result->id;
        }
        $dataResponse = [
            'status' => 1,
            'message' => "Update Profile Successfully",
        ];
        if ($option['task'] == 'edit-item') {
            if (isset($params['image'])) {
                $image = $params['image'];
                $params['image'] = Str::random('10') .  "." . $params['image']->clientExtension();
                $image->storeAs("/user", $params['image'], "rrt_storage");
            }
            $node = self::find($params['id']);
            $firstName = $params['first_name'] ?? null;
            $lastName = $params['last_name'] ?? null;
            if($firstName && $lastName){
                if($firstName !== $node->first_name  || $lastName !== $node->last_name){
                    if($node->count_update_name > 3){
                        $dataResponse['status'] = 0;
                        $dataResponse['message'] = __("You have changed your name more than 3 times");
                        return $dataResponse;
                    }
                    $params['count_update_name'] = $node->count_update_name + 1;
                    $dataResponse['status'] = 1;
                    $count_update_name = 3 - $node->count_update_name;
                    $dataResponse['message'] = __("You only have $count_update_name  name changes left");
                }
            }
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            $node->update($paramsUpdate);
            return $dataResponse;
        }
        if ($option['task'] == 'active-by-token') {
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            self::where('token', $params['token'])->update($paramsUpdate);
        }
        if ($option['task'] == 'thumbnail') {
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            return   self::where('id', $params['user_id'])->update(['thumbnail' => $params['thumbnail']]);
        }
    }
    public function saveUserAdmin($params = [], $option = []){
        if ($option['task'] == 'add-item') {
            if (!isset($params['account_type'])) {
                $params['is_homepage'] = 0;
            }
            $paramsInsert = array_diff_key($params, array_flip($this->crudNotAccepted));
            $parent = self::find($params['parent_id']);
            $result =    self::create($paramsInsert, $parent);
            if (isset($params['account_type'])) {
                if ($params['account_type'] == 'publishing') {
                    $subscription_id = 1;
                    SubscriptionOrderModel::create([
                        'subscription_id' => $subscription_id,
                        'total' => 0,
                        'status' => 'pending',
                        'user_id' => $result->id,
                        'cycle' => 'annually'
                    ]);
                } else if ($params['account_type'] == 'distribution') {
                    $subscription_id = 2;
                    SubscriptionOrderModel::create([
                        'subscription_id' => $subscription_id,
                        'total' => 0,
                        'status' => 'pending',
                        'user_id' => $result->id,
                        'cycle' => 'annually'
                    ]);
                }
                else if ($params['account_type'] == 'seller'){
                    if (isset($params['planType'], $params['cycle'])){
                        $planType = $params['planType']??'';
                        if ($planType=='free_seller'){
                            $subscription_id = 3;
                            SubscriptionOrderModel::create([
                                'subscription_id' => $subscription_id,
                                'total' => 0,
                                'status' => 'pending',
                                'user_id' => $result->id,
                                'cycle' => 'annually'
                            ]);
                        }
                        else{
                            $planId = 3;
                            $cycle = $params['cycle']??"annually";
                            PlanOrderModel::create([
                                'plan_id'=>$planId,
                                'cycle'=>$cycle,
                                'total' => 0,
                                'user_id'=>$result->id,
                            ]);
                        }
                    }
                }
            }
            ContractSettingModel::create([
                'contract_id' => 4,
                'category' => 'regular',
                'status' => 'public',
                'user_id' => $result->id,
                'deliverables' => 'untaggedMp3Wave',
                'enabled' => 1,
            ]);
            ContractSettingModel::create([
                'contract_id' => 3,
                'category' => 'regular',
                'status' => 'public',
                'user_id' => $result->id,
                'deliverables' => 'taggedMp3',
                'enabled' => 1,
            ]);
            ContractSettingModel::create([
                'contract_id' => 5,
                'category' => 'regular',
                'status' => 'public',
                'user_id' => $result->id,
                'deliverables' => 'stems',
                'enabled' => 1,
            ]);
            ContractSettingModel::create([
                'contract_id' => 1,
                'category' => 'free',
                'status' => 'public',
                'user_id' => $result->id,
                'deliverables' => 'taggedMp3',
                'enabled' => 1,
            ]);
            return $result->id;
        }
        if ($option['task'] == 'edit-item') {
            if (isset($params['image'])) {
                $image = $params['image'];
                $params['image'] = Str::random('10') .  "." . $params['image']->clientExtension();
                $image->storeAs("/user", $params['image'], "rrt_storage");
            }
            $node = self::find($params['id']);
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            $node->update($paramsUpdate);
        }
        if ($option['task'] == 'active-by-token') {
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            self::where('token', $params['token'])->update($paramsUpdate);
        }
        if ($option['task'] == 'thumbnail') {
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            return   self::where('id', $params['user_id'])->update(['thumbnail' => $params['thumbnail']]);
        }
    }
    public function deleteItem($params = "", $option = "")
    {
        if ($option['task'] == 'delete') {
            $this->where('id', $params['id'])->delete();
        }
        if ($option['task'] == 'multi-delete') {
            $this->whereIn('id', $params['ids'])->delete();
        }
    }
    public function forgotPassword($token, $password)
    {
        $new_password = rrt_encrypt_password($password);
        $user = $this->where('token', $token)->first();
        $new_token = md5($user->email . time());
        return $user->update(['password' => $new_password, 'token' => $new_token]);
    }
    public function articles()
    {
        return $this->hasMany(ArticleModel::class, 'user_id', 'id');
    }
    public function contracts()
    {
        return $this->hasMany(ContractSettingModel::class, 'user_id', 'id');
    }
    public function tracks()
    {
        return $this->hasMany(TrackModel::class, 'user_id', 'id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItemModel::class, 'user_id', 'id')->whereNotNull('order_id')->with('order')->with('contract_track')->with('tracks');
    }
    public function releaseItems()
    {
        return $this->hasMany(MusicDistributionModel::class, 'user_id', 'id');
    }
    public function getOrderItemsWithAdditionalInfo($params = [])
    {
        $data = $this->orderItems();
        if (isset($params['start']) && isset($params['length'])) {
            $data->skip($params['start'])->take($params['length']);
        }
        $data =  $data->orderBy('id', 'desc')->get()->groupBy('order_id')
            ->map(function ($group) {
                $firstOrderItem = $group->first();
                $order = $firstOrderItem->order;
                $track = $firstOrderItem->tracks;
                $contractTrack = $firstOrderItem->contract_track;
                $fullname = $order['fullname'] ?? "-";
                $email = $order['email'] ?? "-";
                $phone = $order['phone'] ?? "-";
                $orderBuyerInfo = "{$fullname} <br>
                <small>{$phone}</small> <br>
                <small>{$email}</small>
                ";
                $total = $order->total ?? 0;
                $total = rrt_show_price($total);
                $status = $order->status ?? "";
                $status = rrt_show_status($status);
                $paymentName = $order->payment->name ?? "";
                $code = $order->code ?? "";
                $orderID = $order->id ?? "";
                return [
                    'code' => $order->code,
                    'order_id' => $order->id,
                    'orderBuyerInfo' => $orderBuyerInfo,
                    'total' => $total,
                    'created_at' => $order['created_at'] ?? "",
                    'status' => $status,
                    'paymentName' => $paymentName,
                    'routeDetail' => rrt_route('public/studio/sale/detail', ['order_id' => $orderID]),
                    'count' => $group->count() ?? 0,
                    'data' => $group,
                    // Thêm các key khác nếu cần
                ];
            })->toArray();
        $result = [];
        foreach ($data as $key => $item) {
            $result[] = $item;
        }
        return $result;
    }
    public function getReleaseItemsWithAdditionalInfo($params = [])
    {
        $data = $this->releaseItems();
        if (isset($params['start']) && isset($params['length'])) {
            $data->skip($params['start'])->take($params['length']);
        }
        $data =  $data->orderBy('id', 'desc')->get()->groupBy('order_id')
            ->map(function ($group) {
                $code = $group->code ?? "";
                return [
                    'code' => $code,
                    'created_at' => $group['created_at'] ?? "",
                    'routeDetail' => "#",
                    'data' => $group,
                    // Thêm các key khác nếu cần
                ];
            })->toArray();
        $result = [];
        foreach ($data as $key => $item) {
            $result[] = $item;
        }
        return $result;
    }
    public function orders()
    {
        return $this->hasManyThrough(
            OrderItemModel::class,
            TrackModel::class,
            'user_id',    // Khóa ngoại của bảng User
            'track_id',         // Khóa ngoại của bảng Track
            'id',         // Khóa chính của bảng User
            'id'    // Khóa chính của bảng Track
        );
    }
    public function checkAllow()
    {
        $user = rrt_get_user_login();
        $check = $this->where('id', $user['id'])->first()->toArray();
        return $check['is_comment'] ?? 0;
    }
    public function checkUserAccountPayment($user_id)
    {
        $user =  $this->where('id', $user_id)->with('paymentAccount')->first();
        if ($user->paymentAccount) {
            $method =   $user->paymentAccount->paymentmethod->where('is_active', 1)->count();
            return $method;
        }
        return 0;
    }
    public function getMethodAccount($user_id)
    {
        $user =  $this->where('id', $user_id)->with('paymentAccount')->first();
        if ($user->paymentAccount) {
            $method =   $user->paymentAccount->paymentmethod;
            return $method;
        }
        return [];
    }
    public function randomCode()
    {
        do {
            $code = random_int(1000, 9999);
        } while (self::where("validate_code", "=", $code)->first());
        return $code;
    }
    public function subscriptionOrders()
    {
        return $this->hasOne(SubscriptionOrderModel::class, 'user_id')->with('info');
    }
    public function planOrders()
    {
        return $this->hasOne(PlanOrderModel::class, 'user_id')->with('info');
    }
    public function taxtypes()
    {
        return $this->belongsTo(TaxModel::class, 'tax_type');
    }
    public function paymentAccount()
    {
        return $this->hasOne(PaymentAccountModel::class, 'user_id', 'id');
    }
    public function billingAccount()
    {
        return $this->hasOne(BillingAccountModel::class, 'user_id', 'id');
    }
    public function socialmedia()
    {
        return $this->hasMany(SocialMediaModel::class, 'user_id');
    }
    public function getSocialMediaLinks()
    {
        $socialMediaLinks = [];
        $baseUrls = [
            'instagram' => 'https://www.instagram.com/',
            'soundcloud' => 'https://soundcloud.com/',
            'tiktok' => 'https://www.tiktok.com/',
            'facebook' => 'https://www.facebook.com/',
            'twitter' => 'https://twitter.com/',
            'youtube'=>'https://www.youtube.com/'
        ];
        $socialMediaData = $this->socialmedia()->whereNotNull('link')->get();
        foreach ($socialMediaData as $socialMedia) {
            $platform = strtolower($socialMedia->name);
            $username = trim($socialMedia->link, '/');

            if (array_key_exists($platform, $baseUrls)) {
                if (filter_var($username, FILTER_VALIDATE_URL)) {
                    $socialMediaLinks[$platform] = $username;
                } else {
                    $username = preg_replace('/^(https?:\/\/)?(www\.)?('.preg_quote($platform).'\.com\/)?/', '', $username);
                    $socialMediaLinks[$platform] = $baseUrls[$platform] . $username;
                }
            }
        }
        return $socialMediaLinks;
    }


    public function favorite()
    {
        return $this->hasMany(TrackFavouritesModel::class, 'user_id')->whereNotNull('track_id');
    }
    public function follow()
    {
        return $this->hasMany(UserFollowModel::class, 'user_id');
    }
    public function comments($params = [])
    {
        $query = $this->hasMany(TrackCommentModel::class, 'user_id');
        if (isset($params['start']) && isset($params['length'])) {
            $query = $query->skip($params['start'])->take($params['length']);
        }
        return $query->with('tracks')->orderby('id', 'desc');
    }
    public function getTypeUser()
    {
        if ($this->role === 'user') {
            $subscriptionOrder = $this->subscriptionOrders()->first();

            if ($subscriptionOrder) {
                switch ($subscriptionOrder->subscription_id) {
                    case 1:
                        return 'publishing';
                    case 2:
                        return 'distribute';
                    case 3:
                        return 'basic';
                    default:
                        return 'user';
                }
            }
            return 'user';
        } elseif ($this->role === 'seller') {
            return 'seller';
        }
        return 'unknown';
    }
}
