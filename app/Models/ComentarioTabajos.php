<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioTabajos extends Model
{
    use HasFactory;
    protected $table = "comentario_trabajos";
    protected $primaryKey = "id";
    protected $fillable = ['body', 'action', 'autor_id', 'curso_id', 'tarea_id'];

}
