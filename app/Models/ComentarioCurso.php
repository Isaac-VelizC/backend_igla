<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioCurso extends Model
{
    use HasFactory;
    protected $table = "comentario_cursos";
    protected $primaryKey = "id";
    protected $fillable = ['body', 'action', 'autor_id', 'curso_id'];

    public function autor()
    {
        return $this->belongsTo(User::class, 'autor_id');
    }
    
    public function cursoHabilitado()
    {
        return $this->belongsTo(CursoHabilitado::class, 'curso_id');
    }
}
