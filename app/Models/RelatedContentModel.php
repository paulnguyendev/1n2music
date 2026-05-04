<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
#Helper
use Illuminate\Support\Str;


class RelatedContentModel extends Model
{
    protected $table = 'rrt_tracks_relateds';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['data_attributes', '_token', 'confirm_password', 'is_agree', 'visibility_text', 'track_type_id_text', 'unTaggedMp3', 'stems', 'taggedMp3', 'thumbnail_url', 'tags', 'genres', 'track_key_id_text', 'moods', 'invs', 'genres', 'contracts_tracks', 'genres_text', 'invs_text', 'moods_text', 'contracts', 'contractsTotal', 'thumbnail_url', 'unTaggedMp3', 'stems', 'taggedMp3'];
    protected $fillable = ['id', 'track_id', 'name', 'description', 'url', 'created_at', 'url_youtube'];
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
                $result = $query->where('first_name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('last_name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('phone', 'LIKE', "%{$params['search']}%")
                    ->orWhere('username', 'LIKE', "%{$params['search']}%")
                    ->orWhere('email', 'LIKE', "%{$params['search']}%");
            }
            if (isset($params['role'])) {
                $query = $query->where('role', $params['role']);
            }
            if (isset($params['not_id'])) {
                $query = $query->where('id', '!=', $params['not_id']);
            }
            // if (isset($params['with'])) {
            //     $query = $query->with(['plan_order']);
            // }
            $result = $query->with('tracks')->orderBy('id', 'desc')->get();
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
                    $account_type = $params['account_type'] ?? '';
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
            //  dd($paramsUpdate);
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
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
    public function file()
    {
        return $this->hasMany(TrackFileModel::class, 'track_id', 'id');
    }
    public function articles()
    {
        return $this->hasMany(ArticleModel::class, 'user_id', 'id');
    }
    public function tags()
    {
        return $this->belongsToMany(TrackTagModel::class, 'rrt_track_tags', 'track_id', 'name');
    }
    public function genres()
    {
        return $this->belongsToMany(TrackGenresModel::class, 'rrt_tracks_genres', 'track_id', 'genre_id');
    }
    public function moods()
    {
        return $this->belongsToMany(TrackMoodsModel::class, 'rrt_tracks_moods', 'track_id', 'mood_id');
    }
    public function invs()
    {
        return $this->belongsToMany(TrackInvsModel::class, 'rrt_tracks_invs', 'track_id', 'invs_id');
    }
    public function contracts()
    {
        return $this->belongsToMany(TrackContractModel::class, 'rrt_contracts_tracks', 'track_id', 'contact_setting_id');
    }
    public function listContracts()
    {
        return $this->hasMany(TrackContractModel::class, 'track_id', 'id');
    }
    public function listTags()
    {
        return $this->hasMany(TrackTagModel::class, 'track_id', 'id');
    }
    public function listGenres()
    {
        return $this->hasMany(TrackGenresModel::class, 'track_id', 'id');
    }
    public function listMoods()
    {
        return $this->hasMany(TrackMoodsModel::class, 'track_id', 'id');
    }
    public function listInvs()
    {
        return $this->hasMany(TrackInvsModel::class, 'track_id', 'id');
    }

    public function tracks()
    {
        return $this->belongsTo(TrackModel::class, 'track_id');
    }
    public function getFileInfo($data = [], $dataType = "thumbnail", $returnType = "url")
    {
        $result = null;
        $data = is_array($data) ? $data : $data->toArray();
        if ($data) {
            $data = array_filter($data, function ($item) use ($dataType) {
                $type = $item['type'] ?? "";
                if ($type == $dataType) {
                    return $item;
                }
            });
            $data = count($data) > 0 ? array_shift($data) : [];
        }
        $fileName = $data['name'] ?? "";
        $fileUrl =  $fileName ? url('public/uploads/tracks/' . $fileName) : "";
        $fileUrl = $dataType == 'thumbnail' ? rrt_show_thumbnail($fileUrl) : $fileUrl;
        $result = $returnType == 'url' ? $fileUrl : $fileName;
        return $result;
    }
    public function randomCode()
    {
        do {
            $code = random_int(10000000, 99999999);
        } while (self::where("code", "=", $code)->first());
        return $code;
    }
}
