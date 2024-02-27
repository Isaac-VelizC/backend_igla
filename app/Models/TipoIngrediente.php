<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoIngrediente extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    protected $table = "tipo_ingrediente";
    protected $primaryKey = "id";
    protected $fillable = ['nombre'];

    public function ingredientes()
    {
        return $this->hasMany(Ingrediente::class, 'tipo_id','id');
    }

}
