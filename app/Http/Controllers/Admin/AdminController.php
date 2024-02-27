<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function myInfo() {
        try {
            if (Auth::check()) {
                $userId = Auth::user()->id;
                $info = Persona::where('user_id', $userId)->first(); // Usar first() en lugar de get()
    
                if ($info) {
                    $data = [
                        'name' => $info->user->name,
                        'email_user' => $info->user->email,
                        'nombre' => $info->nombre,
                        'ap_paterno' => $info->ap_paterno,
                        'ap_materno' => $info->ap_materno,
                        'ci' => $info->ci,
                        'genero' => $info->genero,
                        'email' => $info->email,
                        'photo' => $info->photo,
                        'rol' => $info->rol,
                        'estado' => $info->estado
                    ];
                    return response()->json($data, 200);
                } else {
                    return response()->json(['error' => 'No se encontró información para este usuario.'], 404);
                }
            } else {
                return response()->json(['error' => 'Usuario no autenticado.'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la Información: ' . $e->getMessage()], 500);
        }
    }

    public function allUsers() {
        try {
            $users = Persona::join('users', 'personas.user_id', '=', 'users.id')
                ->where('users.id', '<>', 1)
                ->select('personas.*')
                ->get();
    
            return response()->json(['users' => $users], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al recuperar usuarios: ' . $e->getMessage()], 500);
        }
    }

    //CONVERTIR A JSON
    public function index() {
        $users = User::all();
        $estudiantes = Estudiante::all();
        $docentes = Docente::all();
        $materias = Curso::all();
        return view('admin.home', compact('users', 'estudiantes', 'docentes', 'materias'));
    }
    public function store(Request $request) {
        $rules = [
            'nombre' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            'ap_pat' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            'ap_mat' => 'nullable|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            'ci' => 'required|string|regex:/^\d{7}(?:-[0-9A-Z]{1,2})?$/|unique:personas,ci|min:7',
            'genero' => 'required|in:Mujer,Hombre,Otro',
            'email' => 'required|email|unique:personas,email',
            'telefono' => 'nullable|string|regex:/^[0-9+()-]{8,15}$/|unique:num_telefonos,numero',
            'rol' => 'required|numeric|exists:roles,id',
        ];
        $request->validate($rules);
        try {
            $user = User::firstOrCreate(
                ['name' => $this->generateUniqueUsername($request->nombre)],
                ['email' => $request->email, 'password' => Hash::make('u.'.$request->ci)]
            );
            $role = Role::findById($request->rol);
        
            if ($role) {
                $user->assignRole($role);
            } else {
                return back()->with('error', 'El rol seleccionado no existe.');
            }
            $pers = Persona::create([
                'user_id' => $user->id,
                'nombre' => $request->nombre,
                'ap_paterno' => $request->ap_pat,
                'ap_materno' => $request->ap_mat,
                'ci' => $request->ci,
                'genero' => $request->genero,
                'email' => $request->email,
                'rol' => 'P'
            ]);
            $pers->numTelefono()->create([
                'numero' => $request->telefono,
            ]);
            $pers->personal()->create([
                'persona_id' => $pers->id,
                'fecha_contratado' => Carbon::now(),
            ]);
            return redirect()->route('admin.personal')->with('success', 'La información se guardo con éxito.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Ocurrio un error. Por favor, inténtalo de nuevo. Detalles: ' . $th->getMessage());
        }
    }
    private function generateUniqueUsername($nombre) {
        $username = strtolower($nombre);
        $numeroAleatorio = mt_rand(1000, 9999);
        return $username . $numeroAleatorio;
    }
}