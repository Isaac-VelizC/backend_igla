<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\InfoController;
use App\Models\Estudiante;
use App\Models\FormaPago;
use App\Models\Pagos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PagosController extends Controller
{
    public function allPagos() {
        $pagos = Pagos::all();
        return View('admin.pagos.index', compact('pagos'));
    }
    public function guardarImprimirPago($id) {
        $pago = Pagos::find($id);
        return view('admin.pagos.pagos_factura', compact('pago'));
    }
    public function habilitarPagosMes() {
        try {
            Artisan::call('app:generar-registros-mensuales');
            $nombreMes = Carbon::now()->format('F');
            return back()->with('success', 'Pagos para el mes ' . $nombreMes . ' habilitados');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al ejecutar el comando: ' . $e->getMessage());
        }
    }
    public function formPagos() {
        $formaPagos = FormaPago::all();
        $fecha = now()->toDateString();
        return view('admin.pagos.form_pago', compact('formaPagos', 'fecha'));
    }
    public function storePagosSimples(Request $request) {
        $rules = [
            'forma' => 'required|numeric|exists:formas_pagos,id',
            'estudiante' => 'required|numeric|exists:estudiantes,id',
            'fecha' => 'required|date',
            'monto' => 'required|min:1',
            'descripcion' => 'nullable|string',
        ];
        $request->validate($rules);
        try {
            Pagos::create([
                'responsable_id' => auth()->user()->id,
                'est_id' => $request->estudiante,
                'forma_id' => $request->forma,
                'metodo_id' => 1,
                'fecha' => $request->fecha,
                'monto' => $request->monto,
                'comentario' => $request->descripcion
            ]);
            $estudi = Estudiante::find($request->estudiante);
            $numeroTelefono = $estudi->persona->numTelefono->numero;
            if ($numeroTelefono) {
                $message = 'Su pago de '. $request->monto .'bs. fue registrado con exito, querido estudiante: '. $estudi->persona->nombre;
                InfoController::notificacionNotaTarea($numeroTelefono, $message);
            }
            return redirect()->route('admin.lista.pagos')->with('success', 'Pago registrado exitosamente.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Ocurrio un errror: ' . $th->getMessage());
        }
    }
}
