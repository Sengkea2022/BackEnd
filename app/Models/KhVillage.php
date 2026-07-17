<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class KhVillage extends Model {
    protected $table = 'kh_villages';
    protected $fillable = ['code','commune_code','name','name_km'];
    public function commune() { return $this->belongsTo(KhCommune::class,'commune_code','code'); }
}
