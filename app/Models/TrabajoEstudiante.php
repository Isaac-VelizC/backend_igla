<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrabajoEstudiante extends Model
{
    use HasFactory;
    protected $table = "trabajo_estudiantes";
    protected $primaryKey = "id";
    protected $fillable = ['curso_id', 'trabajo_id', 'estudiante_id', 'descripcion', 'estado', 'nota'];

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class, 'trabajo_id');
    }
    public function materia()
    {
        return $this->belongsTo(CursoHabilitado::class, 'curso_id');
    }
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }
}
