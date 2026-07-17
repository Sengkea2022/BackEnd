<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class KhProvince extends Model {
    protected $table = 'kh_provinces';
    protected $fillable = ['code','name','name_km','type'];
    public function districts() { return $this->hasMany(KhDistrict::class,'province_code','code'); }
}
