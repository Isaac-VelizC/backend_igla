<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programacion extends Model
{
    use HasFactory;
    protected $table = "programacions";
    protected $primaryKey = "id";
    protected $fillable = ['estudiante_id', 'responsable_id', 'curso_id', 'fecha', 'estado_materia', 'estado'];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
    public function cursoDocente()
    {
        return $this->belongsTo(CursoHabilitado::class, 'curso_id');
    }
    
}
