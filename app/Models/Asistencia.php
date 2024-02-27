<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;
    protected $table = "asistencias";
    protected $primaryKey = "id";
    protected $fillable = ['estudiante_id', 'curso_id', 'asistencia', 'fecha'];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'id');
    }
    public function cursoDocente()
    {
        return $this->belongsTo(CursoHabilitado::class, 'curso_id', 'curso_id');
    }

}
