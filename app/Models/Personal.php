<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "personals";
    protected $primaryKey = "id";
    protected $fillable = ['persona_id', 'estado', 'fecha_contratado', 'sueldo', 'rol'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'id');
    }
}
