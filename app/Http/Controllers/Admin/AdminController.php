<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\NumTelefono;
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
                ->select('personas.*', 'users.name', 'users.email as user_email')
                ->get();
    
            if ($users->isNotEmpty()) {
                $formattedUsers = $users->map(function ($user) {
                    $rolesNames = implode(', ', $user->user->roles->pluck('name')->toArray());
                    return [
                        'id' => $user->id,
                        'nombre' => $user->nombre,
                        'ap_paterno' => $user->ap_paterno,
                        'ap_materno' => $user->ap_materno,
                        'ci' => $user->ci,
                        'telefono' => $user->numTelefono->numero,
                        'role' => $rolesNames,
                        'rol' => $user->rol,
                        'email' => $user->email,
                        'estado' => $user->estado
                    ];
                });
    
                return response()->json(['users' => $formattedUsers], 200);
            } else {
                return response()->json(['error' => 'No se encontraron usuarios'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al recuperar usuarios: ' . $e->getMessage()], 500);
        }
    }

    public function checkEmailUnique($email, $id) {
        try {
            if (!$email) {
                return response()->json(['error' => 'Correo electrónico no proporcionado'], 400);
            }
            
            if ($id == 0) {
                $user = User::where('email', $email)->first();
                $unique = !$user;
            } else {
                $persona = Persona::find($id);
                $user = User::where('email', $email)->where('id', '!=', $persona->user->id)->first();
                $unique = !$user;
            }
    
            return response()->json(['unique' => $unique], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al verificar la unicidad del correo electrónico: ' . $e->getMessage()], 500);
        }
    }

    public function checkCiUnique($ci, $id) {
        try {
            if (!$ci) {
                return response()->json(['error' => 'Cedula de identidad no proporcionado'], 400);
            }
            
            if ($id == 0) {
                $user = Persona::where('ci', $ci)->first();
                $unique = !$user;
            } else {
                $user = Persona::where('ci', $ci)->where('id', '!=', $id)->first();
                $unique = !$user;
            }
    
            return response()->json(['unique' => $unique], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al verificar la unicidad de la cedula de identidad: ' . $e->getMessage()], 500);
        }
    }

    public function checkTelefonoUnique($numero, $id) {
        try {
            if (!$numero) {
                return response()->json(['error' => 'Telefono no proporcionado'], 400);
            }
            if ($id == 0) {
                $num = NumTelefono::where('numero', $numero)->first();
                $unique = !$num;
            } else {
                $num = NumTelefono::where('numero', $numero)->where('id_persona', '!=', $id)->first();
                $unique = !$num;
            }
    
            return response()->json(['unique' => $unique], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al verificar la unicidad del telefono: ' . $e->getMessage()], 500);
        }
    }

    public function storePersonal(Request $request) {
        try {
            $rules = [
                'nombre' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                'ap_paterno' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                'ap_maternot' => 'nullable|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                'ci' => 'required|string|regex:/^\d{7}(?:-[0-9A-Z]{1,2})?$/|unique:personas,ci|min:7',
                'genero' => 'required|in:Mujer,Hombre,Otro',
                'email' => 'required|email|unique:personas,email',
                'telefono' => 'nullable|string|regex:/^[0-9+()-]{8,15}$/|unique:num_telefonos,numero',
                'rol' => 'required|numeric|exists:roles,id',
            ];
        
            $request->validate($rules);
        
            $user = User::firstOrCreate(
                ['name' => $this->generateUniqueUsername($request->nombre)],
                ['email' => $request->email, 'password' => Hash::make('u.'.$request->ci)]
            );
    
            $role = Role::findById($request->rol);
    
            if ($role) {
                $user->assignRole($role);
            } else {
                return response()->json(['error' => 'El rol seleccionado no existe.'], 400);
            }
    
            $pers = Persona::create([
                'user_id' => $user->id,
                'nombre' => $request->nombre,
                'ap_paterno' => $request->ap_paterno,
                'ap_materno' => $request->ap_materno ?: null,
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
    
            return response()->json(['success' => 'La información se guardó con éxito.'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Ocurrió un error. Detalles: ' . $th->getMessage()], 500);
        }
    }

    public function getRoles() {
        try {
            $roles = Role::all();
            if (count($roles) > 0) {
                return response()->json(['roles' => $roles], 200);
            }
            return response()->json(['success' => 'No hay roles.'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error al obtener la Información: ' . $th->getMessage()], 500);
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
    
    private function generateUniqueUsername($nombre) {
        $username = strtolower($nombre);
        $numeroAleatorio = mt_rand(1000, 9999);
        return $username . $numeroAleatorio;
    }
}