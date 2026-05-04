<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecognitionModel extends MasteringModel
{
    protected $table = 'rrt_recognitions';
    protected $fillable = [
        'id',
        'artist',
        'title',
        'album',
        'release_date',
        'label',
        'timecode',
        'song_link',
        'user_id',
        'musicbrainz',
        'apple_music',
        'spotify',
        'deezer',
        'napster',
        'platforms',
        'created_at',
    ];

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

                $query = $query->where('title', 'LIKE', "%{$params['search']}%");
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
                    $item->release_date = $item->release_date ? date('d/m/Y',strtotime($item->release_date)) : '';
                    $item->created_at = $item->created_at ? date('d/m/Y',strtotime($item->created_at)) : '';
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
}
