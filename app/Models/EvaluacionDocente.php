<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionDocente extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "evaluacion_docente";
    protected $primaryKey = "id";
    protected $fillable = ['codigo'];
    
    public function preguntas()
    {
        return $this->hasMany(PreguntaEvaluacionDocente::class, 'id_evaluacion', 'id');
    }
}
