<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class OrderModel extends Model
{
    protected $table = 'rrt_order';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order', 'data_attributes', 'payment_confirmed_at_select', 'redirect'];
    protected $fillable = [
        'id', 'code', 'status', 'fullname', 'country_code' , 'phone', 'email', 'address', 'total', 'total_payment', 'is_refund', 'total_refund', 'coupon', 'point', 'waybill_number', 'fee_delivery', 'fee_additional_shipping', 'note', 'payment_id', 'payment_account_id', 'user_id', 'deliveried_at', 'payment_confirmed_at', 'created_at', 'updated_at'
    ];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];
    use HasFactory;
    public function listItems($params = "", $options = "")
    {
        $result = null;
        $query = $this->select($this->fillable);
        if ($options['task'] == 'admin') {
            if (isset($params['start'])) {
                $query = $query->skip($params['start']);
            }
            if (isset($params['length'])) {
                $query = $query->take($params['length']);
            }
            if (isset($params['search'])) {
                $query = $query->where('code', 'LIKE', "%{$params['search']}%")->orwhere('fullname', 'LIKE', "%{$params['search']}%")->orwhere('email', 'LIKE', "%{$params['search']}%")->orwhere('phone', 'LIKE', "%{$params['search']}%");
            }
            if (isset($params['not_id'])) {
                $query = $query->where('id', '!=', $params['not_id']);
            }
            $result = $query->orderBy('id', 'desc')->get();
            if (isset($params['is_map'])) {
                $result = $result->map(function ($item) use ($params) {
                    $id = $item->id;
                    $item['show_status'] = rrt_show_status($item['status'] ?? '');
                    $item['payment_name'] = $item->payment()->first()->name ?? '';
                    $fullname = $item['fullname'] ?? "";
                    $email = $item['email'] ?? "-";
                    $phone = $item['phone'] ?? "-";
                    $orderBuyerInfo = "{$fullname} <br>
                    <small>Phone: {$phone}</small> <br>
                    <small>{$email}</small>
                    ";
                    $item['order_info'] = $orderBuyerInfo;
                    $orderItems = $item->orderItems() ?? [];
                    $item['order_items_count'] = $orderItems->count() ?? 0;
                    $total = $item->total ?? 0;
                    $total = rrt_show_price($total);
                    $item['show_total'] = $total;
                    #_Route
                    $controllerName = $params['controllerName'] ?? "";
                    $item->route_update = rrt_route($controllerName . "/update", ['id' => $id]);
                    $item->route_detail = rrt_route($controllerName . "/detail", ['id' => $id]);
                    $item->route_form  = rrt_route($controllerName . "/form", ['id' => $id]);
                    $item->route_remove = rrt_route($controllerName . "/delete", ['id' => $id]);
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
            if (isset($params['start']) && isset($params['length'])) {
                $result = $query->orderBy('id', 'desc')->skip($params['start'])->take($params['length'])->get();
            } else {
                if (isset($params['not_id'])) {
                    $query = $query->where('id', '!=', $params['not_id']);
                }
                $result = $query->orderBy('id', 'desc')->get();
            }
        }

        if ($options['task'] == 'get-total-order-status') {
            $status = $params['status'] ?? 'success';
            $result =   $query->whereBetween('created_at', [$params['next_date'], $params['date']])->where('status', $status)->count();
        }

        if ($options['task'] == 'count-order-week-ago') {

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
        return $result;
    }
    public function getItem($params = [], $options = [])
    {
        if ($options['task'] == 'account') {
            $query = $this->select($this->checkEmail);
            $result = $query->where('username', $params['account'])->orWhere('email', $params['account'])->first();
        }
        $query = $this->select($this->fillable);
        if ($options['task'] == 'login') {
            $result = $query->where('email', $params['email'])->where('password', $params['password'])->first();
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
            $result = $query->where('id', $params['id'])->first();
        }
        if ($options['task'] == 'code') {
            $result = $query->where('code', $params['code'])->first();
        }
        if ($options['task'] == 'token') {
            $result = $query->where('token', $params['token'])->first();
        }
        if ($options['task'] == 'identification') {
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
        if ($option['task'] == 'add-item') {
            $paramsInsert = array_diff_key($params, array_flip($this->crudNotAccepted));
            $dataInsert = self::create($paramsInsert);
            $result =  $dataInsert->id;
            return $result;
        }
        if ($option['task'] == 'edit-item') {
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            self::where('id', $params['id'])->update($paramsUpdate);
        }
        if ($option['task'] == 'active-by-token') {
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            self::where('token', $params['token'])->update($paramsUpdate);
        }

        if ($option['task'] == 'save-setting') {
            dd($params);
        }
    }
    public function deleteItem($params = "", $option = "")
    {
        if ($option['task'] == 'delete') {
            $this->where('id', $params['id'])->delete();
        }
    }
    public function articles()
    {
        return $this->hasMany(ArticleModel::class, 'user_id', 'id');
    }
    public function randomCode()
    {
        do {
            $code = random_int(1000, 9999);
        } while (self::where("validate_code", "=", $code)->first());
        return $code;
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItemModel::class, 'order_id', 'id')->with('contract_track')->with('tracks');
    }
    public function orderLogs()
    {
        return $this->hasMany(LogOrderModel::class, 'order_id', 'id');
    }
    public function paymentAccount()
    {
        return $this->belongsTo(OrderPaymentAccountModel::class, 'payment_account_id', 'id');
    }
    public function payment()
    {
        return $this->belongsTo(OrderPaymentModel::class, 'payment_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
}
