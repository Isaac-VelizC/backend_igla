<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocTema extends Model
{
    use HasFactory;
    protected $table = "doc_temas";
    protected $primaryKey = "id";
    protected $fillable = ['nombre', 'url', 'tema_id'];

    public function tema()
    {
        return $this->belongsTo(Tema::class, 'tema_id');
    }
}
