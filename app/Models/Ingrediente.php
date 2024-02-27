<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingrediente extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "ingredientes";
    protected $primaryKey = "id";
    protected $fillable = ['nombre', 'tipo_id'];
    // En el modelo Ingrediente
    public function recetas() {
        return $this->belongsToMany(Receta::class, 'ingrediente_recetas', 'ingrediente_id', 'receta_id')
            ->withPivot(['cantidad', 'unida_media']);
    }
    public function tipo() {
        return $this->belongsTo(TipoIngrediente::class, 'tipo_id');
    }

}
