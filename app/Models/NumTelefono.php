<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumTelefono extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "num_telefonos";
    protected $primaryKey = "id";
    protected $fillable = ['numero', 'id_persona'];
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
}
