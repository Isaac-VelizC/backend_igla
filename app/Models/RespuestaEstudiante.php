<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaEstudiante extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "respuesta_estudiantes";
    protected $primaryKey = "id";
    protected $fillable = ['estudiante_id', 'materia_id', 'cometario', 'fecha'];
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function materia()
    {
        return $this->belongsTo(CursoHabilitado::class, 'materia_id');
    }

    public function evalRespuestas()
    {
        return $this->hasMany(EvalRespuestas::class, 'est_respt_id');
    }
}
