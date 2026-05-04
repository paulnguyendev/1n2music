<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class TrackContractModel extends Model
{
    protected $table = 'rrt_contracts_tracks';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'track_file'];
    protected $fillable = ['id', 'contact_setting_id', 'track_id', 'price', 'enabled'];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];
    use HasFactory;
    public function listItems($params = "", $options = "")
    {
        $result = null;
        $query = $this->select($this->fillable);
        if ($options['task'] == 'count') {
            if ($params['arr_track']) {
                //                                                                           1 Free
                $result =    $this->whereIn('track_id', $params['arr_track'])->where('contact_setting_id', 1)->count();
            }
        }
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
    }
    public function articles()
    {
        return $this->hasMany(ArticleModel::class, 'user_id', 'id');
    }
    public function tracks()
    {
        return $this->belongsToMany(TrackModel::class, 'rrt_tracks_genres', 'mood_id', 'track_id');
    }

    public function contractSetting()
    {
        return $this->belongsTo(ContractSettingModel::class, 'contact_setting_id')->with('contract');
    }
    
    public function randomCode()
    {
        do {
            $code = random_int(10000000, 99999999);
        } while (self::where("code", "=", $code)->first());
        return $code;
    }
}
