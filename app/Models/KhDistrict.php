<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class KhDistrict extends Model {
    protected $table = 'kh_districts';
    protected $fillable = ['code','province_code','name','name_km','type'];
    public function province() { return $this->belongsTo(KhProvince::class,'province_code','code'); }
    public function communes() { return $this->hasMany(KhCommune::class,'district_code','code'); }
}
