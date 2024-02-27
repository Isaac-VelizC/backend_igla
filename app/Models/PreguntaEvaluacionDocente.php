<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreguntaEvaluacionDocente extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "eval_preguntas";
    protected $primaryKey = "id";
    protected $fillable = ['id_evaluacion', 'numero', 'texto'];

    public function evaluacion()
    {
        return $this->belongsTo(EvaluacionDocente::class, 'id_evaluacion', 'id');
    }

    public function respuestas()
    {
        return $this->hasMany(EvalRespuestas::class, 'pregunta_id', 'id');
    }
    
}
