<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryModel extends Model
{
    protected $table = 'countries';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fieldSearchAccepted = ['iso2', 'name', 'status','phone_code', 'iso3', 'region', 'subregion', 'flag'];
    protected $crudNotAccepted = ['_token', 'confirm_password', 'is_agree'];
    protected $fillable = ['iso2', 'name', 'status','phone_code', 'iso3', 'region', 'subregion', 'flag'];
    use HasFactory;
    
}
