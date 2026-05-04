<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
#Helper
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;


class TrackModel extends Model
{

    protected $table = 'rrt_tracks';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'visibility_text', 'track_type_id_text', 'unTaggedMp3', 'stems', 'taggedMp3', 'thumbnail_url', 'tags', 'genres', 'track_key_id_text', 'moods', 'invs', 'genres', 'contracts_tracks', 'genres_text', 'invs_text', 'moods_text', 'contracts', 'contractsTotal', 'thumbnail_url', 'unTaggedMp3', 'stems', 'taggedMp3'];
    protected $fillable = ['is_recommend', 'is_trending','is_featured', 'id', 'code', 'name', 'user_id', 'track_type_id', 'release_date', 'description', 'status', 'visibility', 'thumbnail', 'track_key_id', 'bpm_number', 'updated_at', 'created_at', 'type'];
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
        if ($options['task'] == 'ajax') {
            if (isset($params['user_id'])) {
                $query = $query->where('user_id', $params['user_id']);
            }
            if (isset($params['with']) && !empty($params['with'])) {
                $query = $query->with($params['with']);
            }
            if (isset($params['skip']) && isset($params['take'])) {
                $query = $query->skip($params['skip'])->take($params['take']);
            }

            // dd($params);
            if (isset($params['type'])) {
                if ($params['type'] == 'trending') {
                    $query = $query->where('is_trending', 'checked');
                }
                if ($params['type'] == 'recommend') {
                    $query = $query->where('is_recommend', 'checked');
                }
                if($params['type'=='featured']){
                    $query = $query->where('is_featured', 'checked');
                }
            }

            if (isset($params['user_id'])) {
                // dd($params['user_id']);
                // dd($params);
                $query =  $query->where('user_id', $params['user_id']);
            }

            if (isset($params['search'])) {

                $query =  $query->where('name', 'like', '%' . $params['search'] . '%');
            }

            if (isset($params['genre'])) {

                $genre = $params['genre'];

                $query = $query->whereHas('genres', function ($query) use ($genre) {

                    return   $query->where('genre_id', $genre);
                });
            }
            if (isset($params['tag'])) {
                $tagName = $params['tag'] ?? "";
                $query = $query->whereHas('listTags', function ($query) use ($tagName) {

                    return   $query->where('name', $tagName);
                });
            }
            if (isset($params['mood'])) {
                $moodName = $params['mood'] ?? "";

                $query = $query->whereHas('listMoods', function ($query) use ($moodName) {

                    return   $query->where('name', $moodName);
                });
            }
            if (isset($params['username'])) {
                $username = $params['username'] ?? "";

                $query = $query->whereHas('users', function ($query) use ($username) {

                    return   $query->where('username', $username);
                });
            }
            $result = $query->where('status', 'public')->with('file')->orderBy('id', 'desc')->get();
        }
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
            // if (isset($params['role'])) {
            //     $query = $query->where('role', $params['role']);
            // }
            if (isset($params['not_id'])) {
                $query = $query->where('id', '!=', $params['not_id']);
            }
            $result = $query->orderBy('id', 'desc')->get();
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
                    $user = $item->user;
                    $userName = $user->username??"";
                    $email = $user->email??"";
                    $item->user = $user;
                    $item->fullname = $item->first_name ? $fullname : "-";
                    $item->info = "
                    {$userName}<br>
                    <small><span class = 'text-{$statusClass}'>{$email}<span></small>
                    ";

                    #_Route
                    $controllerName = $params['controllerName'] ?? "";


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
        if ($options['task'] == 'count-track-week-ago') {

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
    // public function getListToTrack($track_id)
    // {
    //     $result =  $this->where('id', $track_id)->with('comment.parentComment.replies')->orderBy('created_at', 'desc')->limit(5);

    //     return $result ?? [];
    // }
    public function comment()
    {
        return $this->hasMany(TrackCommentModel::class, 'track_id')->where('parent_id', 0);
    }


    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
    public function file()
    {
        return $this->hasMany(TrackFileModel::class, 'track_id', 'id');
    }
    public function fileWithType($type)
    {
        return $this->hasMany(TrackFileModel::class, 'track_id', 'id')->where('type', $type);
    }
    public function articles()
    {
        return $this->hasMany(ArticleModel::class, 'user_id', 'id');
    }
    public function tags()
    {
        return $this->belongsToMany(TrackTagModel::class, 'rrt_track_tags', 'track_id', 'name');
    }
    public function relateTags()
    {
        return $this->hasMany(TrackTagModel::class, 'track_id', 'id');
    }
    public function genres()
    {
        return $this->belongsToMany(GenresModel::class, 'rrt_tracks_genres', 'track_id', 'genre_id');
    }
    public function moods()
    {
        return $this->belongsToMany(MoodsModel::class, 'rrt_tracks_moods', 'track_id', 'mood_id');
    }
    public function invs()
    {
        return $this->belongsToMany(InvsModel::class, 'rrt_tracks_invs', 'track_id', 'invs_id');
    }
    public function contracts()
    {
        return $this->belongsToMany(TrackContractModel::class, 'rrt_contracts_tracks', 'track_id', 'contact_setting_id');
    }
    public function listContracts()
    {
        return $this->hasMany(TrackContractModel::class, 'track_id', 'id')
            ->where('enabled', 1)
            ->where('price','>',0)
            ->with('contractSetting.contract');
    }
    public function getSortedContracts()
    {
        return $this->listContracts->sortBy(function($contract) {
            return $contract->contractSetting->contract->order;
        })->values();
    }
    public function favourites()
    {
        return $this->hasMany(TrackFavouritesModel::class, 'track_id', 'id');
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

    public function like()
    {
        return $this->belongsTo(TrackLikeModel::class, 'track_id');
    }
    public function tagInfo()
    {
        return $this->belongsTo(TagModel::class, 'track_id');
    }
    public function download()
    {
        return $this->hasMany(DownloadModel::class, 'track_id');
    }
    public function comments()
    {
        return $this->hasMany(CommentsModel::class, 'track_id');
    }

    public function orderItem()
    {
        return $this->hasMany(OrderItemModel::class, 'track_id');
    }
    public function contractSettings()
    {
        return $this->hasOne(TrackContractSettingModel::class, 'track_id');
    }
    public function users()
    {
        return $this->hasOne(UserModel::class, 'id', 'user_id');
    }

    // New scopes for contract filtering
    public function scopeWithContractData($query)
    {
        return $query->with(['listContracts.contractSetting.contract', 'orderItem', 'file']);
    }

    public function scopePublicTracks($query)
    {
        return $query->where('status', 'public')->where('visibility', 'public');
    }

    public function scopeTrending($query, $limit = 10)
    {
        return $query->publicTracks()
                     ->whereNotNull('is_trending')
                     ->withContractData()
                     ->orderBy('id', 'desc')
                     ->limit($limit);
    }

    public function scopeRecommended($query, $limit = 10)
    {
        return $query->publicTracks()
                     ->whereNotNull('is_recommend')
                     ->withContractData()
                     ->orderBy('id', 'desc')
                     ->limit($limit);
    }

    public function scopeFeatured($query, $limit = 10)
    {
        return $query->publicTracks()
                     ->whereNotNull('is_featured')
                     ->withContractData()
                     ->orderBy('id', 'desc')
                     ->limit($limit);
    }

    public function scopeSearch(Builder $query, array $tagNames = [], string $searchTitle = ''): Builder
    {
        return $query->where(function ($query) use ($tagNames, $searchTitle) {
            if ($searchTitle) {
                $query->whereRaw('MATCH(name, type) AGAINST(? IN NATURAL LANGUAGE MODE)', [$searchTitle]);
            }
            if (!empty($tagNames)) {
                $query->orWhereHas('tags', function ($subQuery) use ($tagNames) {
                    $subQuery->whereRaw('MATCH(name) AGAINST(? IN NATURAL LANGUAGE MODE)', [implode(' ', $tagNames)]);
                });
            }
        });
    }
}
