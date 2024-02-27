<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;
    protected $table = "docentes";
    protected $primaryKey = "id";
    protected $fillable = ['id_persona', 'estado'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
    public function cursos()
    {
        return $this->hasMany(CursoDocente::class, 'docente_id');
    }
}
