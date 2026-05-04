<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class FaqCategoryModel extends Model
{
    protected $table = 'rrt_faq_categories';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'data_attributes', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order'];
    protected $fillable = ['id', 'name', 'created_at'];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];
    use HasFactory, SoftDeletes;
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
                $result = $query->where('name', 'LIKE', "%{$params['search']}%");
                  
            }
            if (isset($params['not_id'])) {
                $query = $query->where('id', '!=', $params['not_id']);
            }
            // if (isset($params['with'])) {
            //     $query = $query->with(['plan_order']);
            // }
            $result = $query->orderBy('id', 'desc')->get();
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
                    $number = $item->faqs()->count();
                    $item->number = $number ? $number  : 0;
                   
                    #_Route
                    $controllerName = $params['controllerName'] ?? "";
                    $controllerNameChild = $params['controllerNameChild'] ?? "";
                    $item->route_update = rrt_route($controllerName . "/update", ['id' => $id]);
                    $item->route_list = rrt_route($controllerNameChild . "/index", ['category' => $id]);
                    $item->route_edit = rrt_route($controllerNameChild . "/index", ['category' => $id]);
                    $item->route_remove = rrt_route($controllerName . "/delete", ['id' => $id]);
                    $item->route_form = rrt_route($controllerName . "/form", ['id' => $id]);
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
        if ($options['task'] == 'ajax') {
            $result = $query->where('status', 'active')->orderBy('id', 'desc')->get();
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
        if ($option['task'] == 'active-by-token') {
            $paramsUpdate = array_diff_key($params, array_flip($this->crudNotAccepted));
            self::where('token', $params['token'])->update($paramsUpdate);
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
    public function articles()
    {
        return $this->hasMany(ArticleModel::class, 'user_id', 'id');
    }
    public function faqs()
    {
        return $this->hasMany(FaqModel::class, 'category_id', 'id');
    }
    public function contracts()
    {
        return $this->hasMany(ContractSettingModel::class, 'user_id', 'id');
    }
    public function tracks()
    {
        return $this->hasMany(TrackModel::class, 'user_id', 'id');
    }
    public function randomCode()
    {
        do {
            $code = random_int(1000, 9999);
        } while (self::where("validate_code", "=", $code)->first());
        return $code;
    }
}
