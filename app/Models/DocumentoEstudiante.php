<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoEstudiante extends Model
{
    use HasFactory;
    protected $table = "documento_estudiantes";
    protected $primaryKey = "id";
    protected $fillable = ['nombre', 'url', 'entrega_id', 'user_id'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function entregas()
    {
        return $this->hasMany(TrabajoEstudiante::class, 'entrega_id');
    }
}
