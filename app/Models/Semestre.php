<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "semestres";
    protected $primaryKey = "id";
    protected $fillable = ['nombre', 'descripcion'];

    public function cursos()
    {
        return $this->hasMany(Curso::class, 'semestre_id');
    }
}
