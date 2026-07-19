<?php namespace App\Models;

use App\Traits\HasQueryScopes;
use Illuminate\Database\Eloquent\Model;

class KhVillage extends Model {
    use HasQueryScopes;

    protected array $searchable = ['code'];

    protected $table = 'kh_villages';
    protected $fillable = ['code','commune_code','name','name_km'];
    public function commune() { return $this->belongsTo(KhCommune::class,'commune_code','code'); }
}
