<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#Helper
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class MasteringModel extends Model
{
    protected $table = 'rrt_masterings';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['email', 'phone', 'fullname'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree', 'cycle', 'plan_order', 'page', 'subscription_order', 'data_attributes','thumbnail_text','redirect','created_at'];
    protected $fillable = ['id', 'name','status','input_audio_id','preview_job_id','process_preview','master_job_id','process_mastering','mastered_file_url','expire_download_at','api_version','container_kind','container_duration','container_size','container_bitrate','audio_codec','audio_channels','audio_sample_rate','audio_duration','audio_bitrate','loudness_measured','loudness_range','loudness_true_peak','eq_levels','stereo_low_width','stereo_mid_width','stereo_high_width', 'bass_preservation','bit_depth','ceiling','ceiling_mode','failure_reason','for_preview','high_cut_freq','limiting_error','limiting_error_spectrogram_image_url','low_cut_freq','mastering','mastering_algorithm','mastering_matching_level','mastering_reverb','mastering_reverb_gain','mode','noise_reduction','output_audio_id','output_format','oversample','preserved','preset','progression','quality','retry_count','review_comment','review_score','sample_rate','target_loudness','target_loudness_mode','user_id','waiting_order','created_at'];
    protected $checkEmail = ['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'fullname', 'email'];
    protected $casts =[
        'eq_levels'=>'array'
    ];
    use HasFactory;
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
            if(isset($params['user_id'])) {
                $query = $query->where('user_id',$params['user_id']);
            }
            if (isset($params['search'])) {

                $query = $query->where('name', 'LIKE', "%{$params['search']}%");
            }
            if (isset($params['not_id'])) {
                $query = $query->where('id', '!=', $params['not_id']);
            }
            if (isset($params['status'])) {
                $query = $query->where('status', $params['status']);
            }
            $result = $query->orderBy('id', 'desc')->get();

            if (isset($params['is_map'])) {
                $result = $result->map(function ($item) use ($params) {
                    $id = $item->id;
                    $item['show_status'] = rrt_show_status($item['status'] ?? '');
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
            if(isset($params['user_id'])) {
                $query = $query->where('user_id',$params['user_id']);
            }
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
        if ($options['task']=='processing'){
            if(isset($params['user_id'])) {
                $query = $query->where('user_id',$params['user_id']);
            }
            $result = $query->whereNotNull('preview_job_id')->whereNotNull('master_job_id')->orderBy('id', 'desc')->get();
            $countNotComplete = $result->where('process_preview', '!=', 100)->where('process_master','!=',100)->count();
            if (isset($params['is_map'])) {
                $result = $result->map(function ($item) use ($params) {
                    $id = $item->id??'';
                    $process_preview = $item->process_preview ??0;
                    $process_mastering = $item->process_mastering ??0;
                    $status = rrt_show_status($item->status ?? 'waiting');
                    return [
                        'id'=>$id,
                        'process_preview'=>$process_preview,
                        'process_mastering'=>$process_mastering,
                        'status'=>$status,
                    ];
                });
            }
            return [
                'data' => $result,
                'processing_count' => $countNotComplete??1
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
        if ($options['task'] == 'token') {
            $result = $query->where('token', $params['token'])->first();
        }
        if ($options['task'] == 'identification') {
            $result = $query->where('identification', $params['identification'])->first();
        }
        if($options['task'] == 'input_audio_id'){
            $result = $query->where('input_audio_id',$params['input_audio_id'])->first();
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

    public function tracks()
    {
        return $this->belongsTo(TrackModel::class, 'track_id');
    }
    public function users()
    {
        return $this->belongsTo(AdminModel::class, 'user_id');
    }
}
