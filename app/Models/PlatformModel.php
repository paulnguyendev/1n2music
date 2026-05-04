<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformModel extends Model
{
    use HasFactory;

    protected $table = 'rrt_platforms';

    protected $fillable = ['name', 'status', 'settings'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree'];
    protected $casts = [
        'settings' => 'array',
    ];

    public function getItem($params = [], $options = [])
    {
        $query = $this->select($this->fillable);
        if ($options['task'] == 'id') {
            $result = $query->where('id', $params['id'])->first();
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
}
