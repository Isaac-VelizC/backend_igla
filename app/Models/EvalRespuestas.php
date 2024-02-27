<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvalRespuestas extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "eval_respuestas";
    protected $primaryKey = "id";
    protected $fillable = ['pregunta_id', 'habilitado_id', 'texto', 'fecha', 'est_respt_id'];
    
    public function pregunta()
    {
        return $this->belongsTo(PreguntaEvaluacionDocente::class, 'pregunta_id', 'id');
    }

    public function habilitadoEvaluacion()
    {
        return $this->belongsTo(EvaluacionHabilitada::class, 'habilitado_id', 'id');
    }

    public function respuestaEstudiante()
    {
        return $this->belongsTo(RespuestaEstudiante::class, 'est_respt_id');
    }
}
