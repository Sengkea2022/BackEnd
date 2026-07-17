<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class KhCommune extends Model {
    protected $table = 'kh_communes';
    protected $fillable = ['code','district_code','name','name_km','type'];
    public function district() { return $this->belongsTo(KhDistrict::class,'district_code','code'); }
    public function villages() { return $this->hasMany(KhVillage::class,'commune_code','code'); }
}
