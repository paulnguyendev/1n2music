<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class BulletinBoardModel extends Model
{
    protected $table = 'rrt_bulletin_boards';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order', 'data_attributes', 'thumbnail_text'];
    protected $fillable = [
        'id',
        'name',
        'code',
        'content',
        'category_id',
        'type',
        'user_id',
        'admin_id',
        'thumbnail',
        'view',
        'desc',
        'created_at',
        'link',
        'language'
    ];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];
    use HasFactory, SoftDeletes;
    public function listItems($params = "", $options = "")
    {
        $result = null;
        $query = $this->select($this->fillable);
        if ($options['task'] == 'bulletinboard-dashboard') {

            $result =   $query->orderBy('id', 'desc')->take(3)->get();
        }
        if ($options['task'] == 'admin') {

            if (isset($params['start'])) {
                $query = $query->skip($params['start']);
            }
            if (isset($params['length'])) {
                $query = $query->take($params['length']);
            }
            if (isset($params['user_id'])) {
                $query = $query->where('user_id', $params['user_id']);
            }

            if (isset($params['search'])) {

                $query = $query->where('name', 'LIKE', "%{$params['search']}%");
            }
            if (isset($params['not_id'])) {
                $query = $query->where('id', '!=', $params['not_id']);
            }
            if (isset($params['bulletin'])){
                $query = $query->whereNull('type');
            }
            $result = $query->orderBy('id', 'desc')->get();
            if (isset($params['is_map'])) {
                $result = $result->map(function ($item) use ($params) {
                    $id = $item->id;
                    #_Route
                    $controllerName = $params['controllerName'] ?? "";

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
            if (isset($params['user_id'])) {
                $query = $query->where('user_id', $params['user_id']);
            }
            $result = $query->orderBy('id', 'desc')->get();
            if (isset($params['count'])) {
                $result = $result->count();
            }
        }
        if ($options["task"]== 'threads'){
//            $query = $query->where('type', '!=', 'free');
            if (isset($params['user_id'])) {
                $query = $query->where('user_id', $params['user_id']);
            }
            $result = $query->whereNull('type')->orderBy('id', 'desc')->get();
            if (isset($params['count'])) {
                $result = $result->count();
            }
        }
        if ($options['task'] == 'free-board'){
            if (isset($params['start'])) {
                $query = $query->skip($params['start']);
            }
            if (isset($params['length'])) {
                $query = $query->take($params['length']);
            }
            if (isset($params['user_id'])) {
                $query = $query->where('user_id', $params['user_id']);
            }
            if (isset($params['category'])){
                $query = $query->where('category_id',$params['category']);
            }
            if (isset($params['search'])) {

                $query = $query->where('name', 'LIKE', "%{$params['search']}%");
            }
            if (isset($params['not_id'])) {
                $query = $query->where('id', '!=', $params['not_id']);
            }
            if (isset($params['paginate'])){
                $result = $query->where('type', 'free')->orderBy('id', 'desc')->paginate(10);
            }
            else{
                $result = $query->where('type', 'free')->orderBy('id', 'desc')->get();
            }
            if (isset($params['is_map'])) {
                $result = $result->map(function ($item) use ($params) {
                    $id = $item->id;
                    #_Route
                    $controllerName = $params['controllerName'] ?? "";

                    $item->route_edit = rrt_route($controllerName . "/form", ['id' => $id]);
                    $item->route_remove = rrt_route($controllerName . "/delete", ['id' => $id]);
                    return $item;
                });
            }
            if (isset($params['count'])) {
                $result = $result->count();
            }
        }
        if ($options['task'] == 'most-view') {
            $result = $query->where('code', '!=', $params['code'])->orderBy('view', 'desc')->limit(10)->get();
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
    protected $with = ['category'];

    public function articles()
    {
        return $this->hasMany(ArticleModel::class, 'user_id', 'id');
    }
    public function category(){
        return $this->belongsTo(BulletinBoardCategoryModel::class,'category_id');
    }
    public function randomCode()
    {
        do {
            $code = random_int(100000, 999999);
        } while (self::where("code", "=", $code)->first());
        return $code;
    }

    public function tracks()
    {
        return $this->belongsTo(TrackModel::class, 'track_id');
    }
    public function users()
    {
        return $this->belongsTo(AdminModel::class, 'admin_id');
    }
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
    public function getAuthorAttribute()
    {
        return $this->user_id ? $this->user : ($this->admin_id ? $this->users : null);
    }
    public function comments()
    {
        return $this->hasMany(BulletinBoardCommentModel::class,'thread_id','id')->whereNull('parent_id');
    }

    public function translations()
    {
        return $this->hasMany(BulletinBoardTranslationModel::class, 'bulletin_board_id');
    }

    public function getTranslatedAttribute($language)
    {
        return $this->translations()->where('language', $language)->first();
    }
}
