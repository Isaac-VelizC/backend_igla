<?php

namespace App\Http\Controllers;

use App\Models\CursoHabilitado;
use Twilio\Rest\Client;

class InfoController extends Controller
{
    public static function notificacionTrabajoPublicado($id, $message)
    {
        $curso = CursoHabilitado::with('inscripciones.estudiante')->find($id);
        $estudiantes = $curso->inscripciones->pluck('estudiante');

        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        try {
            foreach ($estudiantes as $estudiante) {
                $recipientNumber = 'whatsapp:+591' . $estudiante->persona->numTelefono->numero;
                $twilio->messages->create(
                    $recipientNumber,
                    [
                        "from" => "whatsapp:+14155238886",
                        "body" => $message,
                    ]
                );
            }
            return response()->json(['message' => 'Se le notificó a los estudiantes']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public static function notificacionNotaTarea($num, $message) {
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        try {
            $recipientNumber = 'whatsapp:+591' . $num;
            $twilio->messages->create(
                $recipientNumber,
                [
                    "from" => "whatsapp:+14155238886",
                    "body" => $message,
                ]
            );
            return response()->json(['message' => 'Se le notificó a los estudiantes']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
