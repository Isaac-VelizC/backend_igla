<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;
    protected $table = "recetas";
    protected $primaryKey = "id";
    protected $fillable = ['titulo', 'imagen', 'descripcion', 'porcion', 'tiempo', 'ocasion', 'consejos'];

    public function pasos() {
        return $this->hasMany(PasosReceta::class, 'receta_id');
    }
    // En el modelo Receta
    public function ingredientesReceta() {
        return $this->hasMany(IngredienteReceta::class, 'receta_id');
    }
    public function ingredientes() {
        return $this->belongsToMany(Ingrediente::class, 'ingrediente_recetas', 'receta_id', 'ingrediente_id')
            ->withPivot(['cantidad', 'unida_media']);
    }
}
class IngredienteReceta extends Model
{
    use HasFactory;
    protected $table = "ingrediente_recetas";
    protected $primaryKey = "id";
    protected $fillable = ['ingrediente_id', 'cantidad', 'unida_media', 'receta_id'];
    // En el modelo IngredienteReceta
    public function receta() {
        return $this->belongsTo(Receta::class, 'receta_id');
    }
    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class, 'ingrediente_id');
    }
}
class PasosReceta extends Model
{
    use HasFactory;
    protected $table = "pasos_recetas";
    protected $primaryKey = "id";
    protected $fillable = ['paso', 'numero', 'receta_id'];    
    // En el modelo PasosReceta
    public function receta() {
        return $this->belongsTo(Receta::class, 'receta_id');
    }

}