<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class RequestPayoutModel extends Model
{
    // use NodeTrait;
    protected $table = 'rrt_request_payouts';
    protected $primaryKey = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order'];
    protected $fillable = [
        'transaction_id',
        'id', 'user_id', 'status', 'created_at', 'updated_at',
        'owner', 'seller', 'manager', 'method_payment', 'tax_type',
        'withdrawal_method', 'amount_tax', 'note', 'amount_supply',
        'vat', 'amount_request', 'amount_report', 'memo', 'amount_payment', 'created_at', 'updated_at'
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
                $result = $query->where('first_name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('last_name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('phone', 'LIKE', "%{$params['search']}%")
                    ->orWhere('username', 'LIKE', "%{$params['search']}%")
                    ->orWhere('email', 'LIKE', "%{$params['search']}%");
            }

            // if (isset($params['with'])) {
            //     $query = $query->with(['plan_order']);
            // }

            $result = $query->with('users')->orderBy('id', 'desc')->get();
            if (isset($params['is_map'])) {
                $result = $result->map(function ($item) use ($params) {
                    $item->xhtml_status = rrt_show_status($item->status);
                    $item->amount_request =  rrt_show_price($item->amount_request);
                    $item->amount_supply =  rrt_show_price($item->amount_supply);
                    $item->vat =  rrt_show_price($item->vat);
                    $item->amount_tax =  rrt_show_price($item->amount_tax);
                    $item->amount_payment =  rrt_show_price($item->amount_payment);
                    $item->amount_report =  rrt_show_price($item->amount_report);
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
                    #_Route
                    $controllerName = $params['controllerName'] ?? "";
                    $account_type = $params['account_type'] ?? '';
                    $item->route_update = rrt_route($controllerName . "/update", ['id' => $id]);
                    $item->route_edit = rrt_route($controllerName . "/form", ['id' => $id]);
                    $item->route_remove = rrt_route($controllerName . "/delete", ['id' => $id]);
                    $item->route_detail =   rrt_route($controllerName . '/detail', ['id' =>  $id]);
                    $item->route_approve =   rrt_route($controllerName . '/postApprove', ['id' =>  $id]);
                    $item->tax = $item->users->taxtypes->name ?? '-';
                    $item->method_payment = $item->method ? ucfirst($item->method->method) : '-';
                    $item->seller = ucfirst($item->users->role ?? "-");
                    $item->manager = rrt_get_fullname_by_user($item->users);
                    $item->time = rrt_convert_format_date($item->created_at ?? '', 'd-m-Y H:i:s');
                    $item->route_cancel = rrt_route($controllerName . '/postCancel', ['id' => $id]);
                    return $item;
                });
            }
            if (isset($params['count'])) {
                $result = $result->count();
            }
        }
        if ($options['task'] == 'all') {
            if (isset($params['status'])) {
                $query = $query->where('status', $params['status']);
            }
            if (isset($params['limit'])) {
                $query = $query->limit($params['limit']);
            }
            $result = $query->orderBy('id', 'desc')->get();
            if (isset($params['count'])) {
                $result = $result->count();
            }
        }
        if ($options['task'] == 'list') {
            if (isset($params['status'])) {
                $query = $query->where('status', $params['status']);
            }
            if (isset($params['role'])) {
                $query = $query->where('role', $params['role']);
            }
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
            $result = $query->where('status', 'active')->orderBy('id', 'desc')->get();
        }
        if ($options['task'] == 'send-mail') {
            $query = $this->select($this->checkEmail);
            $result = $query->orderBy('id', 'desc')->get();
            if (isset($params['count'])) {
                $result = $result->count();
            }
        }
        if ($options['task'] == 'studio') {
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

            // if (isset($params['with'])) {
            //     $query = $query->with(['plan_order']);
            // }

            $result = $query->where('user_id', $params['user_id'])->with('users')->with('method')->orderBy('id', 'desc')->get();
            //$result = $query->with('users')->orderBy('id', 'desc')->get();
            if (isset($params['is_map'])) {
                $result = $result->map(function ($item) use ($params) {
                    $item->xhtml_status = rrt_show_status($item->status);
                    $item->amount_request =  rrt_show_price($item->amount_request);
                    $item->amount_supply =  rrt_show_price($item->amount_supply);
                    $item->vat =  rrt_show_price($item->vat);
                    $item->amount_tax =  rrt_show_price($item->amount_tax);
                    $item->amount_payment =  rrt_show_price($item->amount_payment);
                    $item->amount_report =  rrt_show_price($item->amount_report);
                    $item->tax = $item->users->taxtypes ?  $item->users->taxtypes->name : '';
                    $item->method_payment = $item->method ? ucfirst($item->method->method) : '';
                    $item->time = rrt_convert_format_date($item->created_at ?? '', 'd-m-Y H:i:s');
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
                    #_Route
                    $controllerName = $params['controllerName'] ?? "";
                    $account_type = $params['account_type'] ?? '';

                    $item->route_detail =   rrt_route($controllerName . '/detail', ['id' =>  $id]);
                    return $item;
                });
            }
            if (isset($params['count'])) {
                $result = $result->count();
            }
        }

        if ($options['task'] == 'count-request-week-ago') {

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
        $result = null;
        if ($options['task'] == 'id_record') {
            $result =   $this->where('id', $params['id'])->first();
        }
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
            $parent = self::find($params['parent_id']);
            $result =    self::create($paramsInsert, $parent);
            return $result;
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
    }

    public function addItemFromTransaction($params)
    {
        $this->create([
            'user_id'           => $params['user_id'],
            'status'            => $params['status'],
            'owner'             => $params['owner'],
            'seller'            => $params['seller'],
            'manager'           => $params['manager'],
            'method_payment'    => $params['method_payment'],
            'tax_type'          => $params['tax_type'],
            'withdrawal_method' => $params['withdrawal_method'],
            'amount_tax'        => $params['amount_tax'],
            'amount_supply'     => $params['amount_supply'],
            'vat'               => $params['vat'],
            'amount_request'    => $params['amount_request'],
            'amount_report'     => $params['amount_report'],
            'amount_payment'    => $params['amount_payment'],
            'transaction_id'    => $params['transaction_id'],
        ]);
    }



    public function cancelRequest($id)
    {
        return   $this->where('id', $id)->update(['status' => 'cancel']);
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
    public function contracts()
    {
        return $this->hasMany(ContractSettingModel::class, 'user_id', 'id');
    }
    public function tracks()
    {
        return $this->hasMany(TrackModel::class, 'user_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }


    public function paymentAccount()
    {
        return $this->belongsTo(PaymentAccountModel::class, 'user_id', 'user_id');
    }


    public function log()
    {
        return $this->hasMany(LogRequestPayoutModel::class, 'request_payout_id', 'id');
    }

    public function method()
    {
        return $this->belongsTo(PayoutMethodModel::class, 'withdrawal_method');
    }
}
