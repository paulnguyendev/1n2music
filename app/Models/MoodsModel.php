<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class MoodsModel extends Model
{
    protected $table = 'rrt_moods';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'data_attributes','created_at', 'confirm_password', 'is_agree', 'track_file'];
    protected $fillable = ['id', 'name'];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];
    use HasFactory, SoftDeletes;
    public function listItems($params = "", $options = "")
    {
        $result = null;
        $query = $this->select($this->fillable);
        if ($options['task'] == 'list') {
            if (isset($params['start']) && isset($params['length'])) {
                $result = $query->orderBy('id', 'desc')->skip($params['start'])->take($params['length'])->get();
            } else {
                if (isset($params['not_id'])) {
                    $query = $query->where('id', '!=', $params['not_id']);
                }
                if (isset($params['order_number'])) {
                    $query = $query->orderBy('order_number', 'asc');
                } else {
                    $query = $query->orderBy('id', 'desc');
                }
                $result = $query->get();
            }
        }
        if ($options['task'] == 'admin') {
            if (isset($params['start'])) {
                $query = $query->skip($params['start']);
            }
            if (isset($params['length'])) {
                $query = $query->take($params['length']);
            }
            if (isset($params['search'])) {
                $result = $query->where('name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('ip', 'LIKE', "%{$params['search']}%");
                  
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
                    #_Information
                    $fullname = $item->first_name . " " . $item->last_name;
                    $item->fullname = $item->first_name ? $fullname : "-";
                    $item->info = "
                    {$fullname}<br>
                    <small><span class = 'text-{$statusClass}'>{$statusName}<span></small> 
                    ";
                    #_Route
                    $controllerName = $params['controllerName'] ?? "";
                    $item->route_update = rrt_route($controllerName . "/update", ['id' => $id]);
                    $item->route_edit = rrt_route($controllerName . "/form", ['id' => $id]);
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
            $result = $query->where('id', $params['id'])->first();
        }
        if ($options['task'] == 'token') {
            $result = $query->where('token', $params['token'])->first();
        }
        if ($options['task'] == 'slug') {
            $result = $query->where('slug', $params['slug'])->first();
        }
        if ($options['task'] == 'code') {
            $result = $query->where('code', $params['code'])->first();
        }
        if ($options['task'] == 'check') {
            $result = $query->where('track_id', $params['track_id'])->Where('type', $params['type'])->first();
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
    public function randomCode()
    {
        do {
            $code = random_int(10000000, 99999999);
        } while (self::where("code", "=", $code)->first());
        return $code;
    }
}
