<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;
    protected $table = "inventarios";
    protected $primaryKey = "id";
    protected $fillable = ['ingrediente_id', 'cantidad', 'unidad_media', 'fecha_registro', 'fecha_modificacion', 'fecha_caducidad', 'estado'];

    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class, 'ingrediente_id');
    }
    public function historiales()
    {
        return $this->hasMany(HistorialInventario::class, 'inventario_id');
    }
    public function materiasIngredientes()
    {
        return $this->hasMany(IngredientesCurso::class, 'inventario_id');
    }
}
