<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;
    protected $table = "calificacions";
    protected $primaryKey = "id";
    protected $fillable = ['estudiante_id', 'curso_id', 'num_trabajos', 'num_evaluaciones', 'calificacion'];

    public function cursoHabilitado()
    {
        return $this->belongsTo(CursoHabilitado::class, 'curso_id');
    }
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }
}
