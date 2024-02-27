<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoDocente extends Model
{
    use HasFactory;
    protected $table = "documento_docentes";
    protected $primaryKey = "id";
    protected $fillable = ['nombre', 'url', 'user_id', 'tarea_id'];
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function tarea()
    {
        return $this->belongsTo(Trabajo::class, 'tarea_id');
    }
}
