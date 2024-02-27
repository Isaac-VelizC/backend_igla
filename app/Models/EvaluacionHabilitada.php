<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionHabilitada extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "habilitado_evaluacion";
    protected $primaryKey = "id";
    protected $fillable = ['materia_id', 'eval_docente_id', 'fecha', 'estado'];
    
    public function materia()
    {
        return $this->belongsTo(CursoHabilitado::class, 'materia_id', 'id');
    }

    public function evaluacionDocente()
    {
        return $this->belongsTo(EvaluacionDocente::class, 'eval_docente_id', 'id');
    }
}