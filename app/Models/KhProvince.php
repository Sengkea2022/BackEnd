<?php namespace App\Models;

use App\Traits\HasQueryScopes;
use Illuminate\Database\Eloquent\Model;

class KhProvince extends Model {
    use HasQueryScopes;

    protected array $searchable = ['code'];

    protected $table = 'kh_provinces';
    protected $fillable = ['code','name','name_km','type'];
    public function districts() { return $this->hasMany(KhDistrict::class,'province_code','code'); }
}
