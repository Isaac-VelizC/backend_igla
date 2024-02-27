<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaPago extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "formas_pagos";
    protected $primaryKey = "id";
    protected $fillable = ['nombre'];
    
    public function pagos()
    {
        return $this->hasMany(Pagos::class, 'forma_id');
    }
}
