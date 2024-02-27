<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientesCurso extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "ingredientes_cursos";
    protected $primaryKey = "id";
    protected $fillable = ['cantidad','curso_id', 'inventario_id', 'descripcion', 'fecha', 'estado'];

    public function materia()
    {
        return $this->belongsTo(CursoHabilitado::class, 'curso_id');
    }
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }
}
