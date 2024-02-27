<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoMensual extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "pago_mensuals";
    protected $primaryKey = "id";
    protected $fillable = ['estudiante_id', 'mes', 'anio', 'fecha', 'pagado'];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }
    
    public function pago()
    {
        return $this->belongsTo(Pagos::class, 'id', 'pagoMes_id');
    }

}
