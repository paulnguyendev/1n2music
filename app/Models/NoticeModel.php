<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class NoticeModel extends Model
{
    protected $table = 'rrt_notices';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const IS_READ = 1;
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order', 'data_attributes'];
    protected $fillable = ['track_id', 'type', 'id', 'name', 'description', 'content', 'user_id', 'admin_id', 'created_at', 'updated_at', 'is_other','status'];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];
    use HasFactory, SoftDeletes;
    public function listItems($params = "", $options = "")
    {
        $result = null;
        $query = $this->select($this->fillable);

        if(isset($params['is_other'])){
            $query = $query->where(['is_other' => $params['is_other']]);
        }

        if ($options['task'] == 'dashboard') {
            $user_id = isset($params['user_id']) ? $params['user_id'] : rrt_get_user_login('id');

            $result =  $query->with('tracks')->orderBy('updated_at', 'desc')
            ->where(function($query) {
                $query->whereNull('status')
                      ->orWhere('status', 0);
            })
            ->where('admin_id', $user_id)->with('users')->take(5)->get();
        }
        if ($options['task'] == 'admin') {
            if (isset($params['start'])) {
                $query = $query->skip($params['start']);
            }
            if (isset($params['length'])) {
                $query = $query->take($params['length']);
            }
            if (isset($params['search'])) {
                $query = $query->where('name', 'LIKE', "%{$params['search']}%");
            }
            if (isset($params['not_id'])) {
                $query = $query->where('id', '!=', $params['not_id']);
            }
            $result = $query->orderBy('id', 'desc')->get();
            if (isset($params['is_map'])) {
                $result = $result->map(function ($item) use ($params) {
                    $id = $item->id;
                    #_Route
                    $controllerName = $params['controllerName'] ?? "";
                    $item->route_update = rrt_route($controllerName . "/update", ['id' => $id]);
                    if(isset($params['is_other']) && $params['is_other'] == 1){
                        $item->route_edit = rrt_route($controllerName . "/other/form", ['id' => $id]);
                    }else{
                        $item->route_edit = rrt_route($controllerName . "/form", ['id' => $id]);
                    }
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

        if ($option['task'] == 'favourite') {
            $track = TrackModel::where('id', $params['track_id'])->first();
            if (!$track) {
                return;
            }
            $notice = $this->where('admin_id', $track->user_id)
                ->where('user_id', $params['user_id'])
                ->where('track_id', $track->id)->where('type', 'favourite')
                ->first();
            if ($notice) {

                $notice->update(['updated_at' => $params['create_at']]);
            } else {

                $this->create([
                    'user_id' => $params['user_id'],
                    'admin_id' => $track->user_id,
                    'type' => 'favourite',
                    'created_at' => $params['create_at'],
                    'updated_at' => $params['create_at'],
                    'track_id' => $track->id,
                ]);
            }
        }
        if ($option['task'] == 'follow') {
            $notice = $this->where('admin_id', $params['user_id'])
                ->where('user_id', $params['follow_by_user_id'])
                ->where('type', 'follow')
                ->first();
            if ($notice) {
                $notice->update(['updated_at' => $params['create_at']]);
            } else {

                $this->create([
                    'user_id' => $params['follow_by_user_id'],
                    'admin_id' => $params['user_id'],
                    'type' => 'follow',
                    'created_at' => $params['create_at'],
                    'updated_at' => $params['create_at'],
                ]);
            }
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
            $code = random_int(1000, 9999);
        } while (self::where("validate_code", "=", $code)->first());
        return $code;
    }

    public function users()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
    public function tracks()
    {
        return $this->belongsTo(TrackModel::class, 'track_id');
    }
}
