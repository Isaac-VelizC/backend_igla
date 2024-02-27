<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoCurso extends Model
{
    use HasFactory;
    protected $table = "documento_cursos";
    protected $primaryKey = "id";
    protected $fillable = ['nombre', 'url', 'curso_id', 'user_id'];
    public function curso()
    {
        return $this->belongsTo(CursoHabilitado::class, 'curso_id');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
