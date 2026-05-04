<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class MusicDistributionModel extends Model
{
    protected $table = 'rrt_music_distribution';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'shopIds', 'genreIds','moods', 'releaseDate', 'track_file','secondReleaseDate'];
    protected $fillable = [
        'id', 'code','title_track','artist_name','explicit_content','producers','composers','lyricists','isrc_code','upc_code','label','publishing_information','distribution_information','keywords','description','sns_link','catalog_number', 'type', 'status', 'genre_id','subgenre_id', 'shop_id', 'user_id', 'release_date','2nd_release_date', 'name', 'copyright', 'artist_id', 'label_id', 'thumbnail', 'created_at', 'updated_at'
    ];
    protected $casts = [
        'producers'=>'array',
        'composers'=>'array',
        'lyricists'=>'array',
        'keywords'=>'array',
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
            if (isset($params['type'])) {

                $query = $query->where('type',  $params['type']);
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
                    $item['status'] = __($item['status']??'');
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
                $query = $query->orderBy('id', 'desc')->skip($params['start'])->take($params['length']);
            }
            if (isset($params['not_id'])) {
                $query = $query->where('id', '!=', $params['not_id']);
            }
            if (isset($params['user_id'])) {
                $query = $query->where('user_id',  $params['user_id']);
            }
            if (!empty($params['type'] )) {

                $query = $query->where('type',  $params['type']);
            }
            if (!empty($params['limit'] )) {
                $query = $query->limit($params['limit']);
            }
            if(!empty($params['platform'])){
                $query = $query->whereHas('listPlatform', function ($query) use ($params) {
                    $query->where('platform_id', $params['platform']);
                });
            }
            if (isset($params['search'])) {
                $query = $query->where('code', 'LIKE', "%{$params['search']}%");
            }
            $result = $query->orderBy('id', 'desc')->get();
            if (isset($params['is_map']) && $params['is_map'] ==  1) {
              
                $result = $result->map(function ($item) use ($params,$options) {
                    $id = $item->id;
                    $code = $item->code;
                    $type = $item->type;


                    $genereItems = $item->listGenres()->with('info')->get()->map(function ($genre) {
                        return __($genre['info']['name']??'');
                    })->implode(', ');
                    $shopItems = $item->listPlatform()->with('info')->get()->map(function ($genre) {
                        return $genre['info']['name'];
                    })->implode(', ');
                    $item->generes = $genereItems;
                    $item->shopes = $shopItems;
                    $status = __($item['status'] ?? "");
                    if ($status == 'approved') {
                        $statusClass = 'success';
                    } elseif ($status == 'denied') {
                        $statusClass = 'danger';
                    } else {
                        $statusClass = 'warning';
                    }
                    $item->status_class = $statusClass;
                    $item->status = $status;
                    $item->totalTrack = $item->listTracks()->count();
                    #_Route
                    $controllerName = $params['controllerName'] ?? "";
                    $item->routeDetail = in_array($status, ['complete', 'cancel']) ? "#" : rrt_route($controllerName . "/delivery", ['code' => $code, 'type' => $type]);
                    $area = $params['area'] ?? null;
                    if( $area ) {
                        $item->route_remove = rrt_route($controllerName . "/delete", ['id' => $id]);
                    }
                  

                    return $item;
                });
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
            $code = random_int(100000, 999999);
        } while (self::where("code", "=", $code)->first());
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
    public function shopes()
    {
        return $this->belongsToMany(MusicShopModel::class, 'rrt_music_distribution_shop_relation', 'music_distribution_id', 'shop_id');
    }
    public function platform(){
        return $this->belongsToMany(PlatformModel::class,'rrt_music_distribution_platform_relation','music_distribution_id','platform_id');
    }
    public function moods(){
        return $this->belongsToMany(MoodsModel::class,'rrt_music_distribution_moods_relation','music_distribution_id','mood_id');
    }
    public function genres()
    {
        return $this->belongsToMany(MusicGeneresModel::class, 'rrt_music_distribution_genres_relation', 'music_distribution_id', 'genre_id');
    }
    public function listGenres()
    {
        return $this->hasMany(MusicGeneresModel::class, 'music_distribution_id', 'id');
    }
    public function listShopes()
    {
        return $this->hasMany(MusicShopModel::class, 'music_distribution_id', 'id');
    }
    public function listTracks()
    {
        return $this->hasMany(MusicDistributionTrackModel::class, 'music_distribution_id', 'id');
    }
    public function listPlatform(){
        return $this->hasMany(MusicDistributionPlatform::class,'music_distribution_id','id');
    }

    public static function get_meta_key($type){
        $data = [];
        if ($type == 'single') {
            $data = [
                'limit_upload' => 3,
                'meta_key' => 'limit_single_upload',
            ];
        }
        if ($type == 'album') {
            $data = [
                'limit_upload' => -1,
                'meta_key' => 'limit_album_upload',
            ];
        }
        return $data;
    }
}
