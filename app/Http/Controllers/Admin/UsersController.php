<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CursoHabilitado;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\Horario;
use App\Models\NumTelefono;
use App\Models\Persona;
use App\Models\Personal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function estudiantesAll() {
        try {
            $estudiantes = Estudiante::with('persona')->get();
            return response()->json(['estudiantes' => $estudiantes], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al recuperar los estudiantes: ' . $e->getMessage()], 500);
        }
    }
    
    public function allDocentes() {
        try {
            $docentes = Persona::whereHas('docente')->get();
            if ($docentes->isNotEmpty()) {
                $formattedDocentes = $docentes->map(function ($item) {
                    $rolesNames = implode(', ', $item->user->roles->pluck('name')->toArray());
                    return [
                        'id' => $item->id,
                        'nombre' => $item->nombre,
                        'ap_paterno' => $item->ap_paterno,
                        'ap_materno' => $item->ap_materno,
                        'ci' => $item->ci,
                        'telefono' => $item->numTelefono->numero,
                        'role' => $rolesNames,
                        'rol' => $item->rol,
                        'genero' => $item->genero,
                        'photo' => $item->photo,
                        'email' => $item->email,
                        'estado' => $item->docente->estado
                    ];
                });
    
                return response()->json(['docentes' => $formattedDocentes], 200);
            } else {
                return response()->json(['error' => 'No se encontraron docentes'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al recuperar los docentes: ' . $e->getMessage()], 500);
        }
    }
    
    public function allPersonal() {
        try {
            $personals = Personal::all();
            $roles = Role::whereIn('name', ['Admin', 'Secretario/a'])->get();
            if ($personals->isNotEmpty()) {
                $formattedpersonals = $personals->map(function ($personal) {
                    $rolesNames = implode(', ', $personal->persona->user->roles->pluck('name')->toArray());
                    return [
                        'id' => $personal->persona->id,
                        'nombre' => $personal->persona->nombre,
                        'ap_paterno' => $personal->persona->ap_paterno,
                        'ap_materno' => $personal->persona->ap_materno,
                        'ci' => $personal->persona->ci,
                        'telefono' => $personal->persona->numTelefono->numero,
                        'role' => $rolesNames,
                        'rol' => $personal->rol,
                        'genero' => $personal->persona->genero,
                        'photo' => $personal->persona->photo,
                        'email' => $personal->persona->email,
                        'estado' => $personal->estado,
                        'sueldo' => $personal->sueldo,
                        'fecha_contrato' => $personal->fecha_contratado,
                    ];
                });
                return response()->json(['personals' => $formattedpersonals, 'roles' => $roles], 200);
            } else {
                return response()->json(['error' => 'No se encontraron docentes'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al recuperar el personal: ' . $e->getMessage()], 500);
        }
    }

    public function showEstudiante($id) {
        try {
            $estudiante = Persona::find($id);
            if (!$estudiante) {
                return response()->json(['error' => 'Estudiante no encontrado'], 404);
            }
            $est = Estudiante::where('persona_id', $estudiante->id)->first();
            if (!$est) {
                return response()->json(['error' => 'Información de estudiante no encontrada'], 404);
            }
            $horarios = Horario::all();
            $materias = CursoHabilitado::all();
            $data = [
                'estudiante' => $estudiante,
                'est' => $est,
                'horarios' => $horarios,
                'materias' => $materias
            ];
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al recuperar información del estudiante: ' . $e->getMessage()], 500);
        }
    }
    
    public function selectEstudiante(Request $request) {
        $query = $request->input('name');

        $estudiantes = Estudiante::where(function ($queryBuilder) use ($query) {
            $queryBuilder->whereHas('persona', function ($q) use ($query) {
                $q->where('nombre', 'like', "%$query%")
                ->orWhere('ap_paterno', 'like', "%$query%")
                ->orWhere('ap_materno', 'like', "%$query%")
                ->orWhere('ci', 'like', "%$query%");
            });
        })->with('persona')->get();

        return response()->json($estudiantes);
    }
    
    public function gestionarEstadoEstudiante($id, $accion) {
        $persona = Persona::find($id);
        if ($persona) {
            $estado = ($accion === 'baja') ? false : true;
            $persona->update(['estado' => $estado]);
            Estudiante::where('persona_id', $id)->update(['estado' => $estado]);    
            $mensaje = ($accion === 'baja') ? 'Se dio de baja al estudiante' : 'Se dio de alta al estudiante';
            return response()->json(['success' => $mensaje], 200);
        } else {
            return response()->json(['error' => 'No se encontró la persona'], 404);
        }
    }
    
    public function gestionarEstadoDocente($id, $accion) {
        $persona = Persona::find($id);
        if ($persona) {
            $estado = ($accion === 'baja') ? false : true;
            $persona->update(['estado' => $estado]);
            Docente::where('id_persona', $id)->update(['estado' => $estado]);
            $mensaje = ($accion === 'baja') ? 'Se dio de baja al docente' : 'Se dio de alta al docente';
            return response()->json(['success' => $mensaje], 200);
        } else {
            return response()->json(['error' => 'No se encontró la persona'], 404);
        }
    }
    
    public function gestionarEstadoPersonal($id, $accion) {
        $persona = Persona::find($id);
        if ($persona) {
            $estado = ($accion === 'baja') ? false : true;
            $persona->update(['estado' => $estado]);
            Personal::where('persona_id', $id)->update(['estado' => $estado]);
            $mensaje = ($accion === 'baja') ? 'Se dio de baja al personal' : 'Se dio de alta al personal';
            return response()->json(['success' => $mensaje], 200);
        } else {
            return response()->json(['error' => 'No se encontró la persona'], 404);
        }
    }
    
    public function updateDocenteInfo(Request $request, $id) {
        try {
            $rules = [
                'nombre' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                'ap_paterno' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                'ap_materno' => 'nullable|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                'ci' => 'required|string|regex:/^\d{7}(?:-[0-9A-Z]{1,2})?$/|unique:personas,ci,' . $id,
                'genero' => 'required',
                'email' => 'required|email',
                'telefono' => 'nullable|string|regex:/^[0-9+()-]{8,15}$/',
            ];
            $request->validate($rules);
            $docente = Persona::find($id);
            if (!$docente) {
                return response()->json(['error' => 'No se encontró al docente'], 404);
            }
            $docente->update([
                'nombre' => $request->nombre,
                'ap_paterno' => $request->ap_paterno,
                'ap_materno' => $request->ap_materno,
                'ci' => $request->ci,
                'genero' => $request->genero,
                'email' => $request->email,
            ]);
            if ($request->telefono) {
                NumTelefono::updateOrCreate(
                    ['id_persona' => $id],
                    ['numero' => $request->telefono]
                );
            }
    
            return response()->json(['success' => 'La información se actualizó con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Hubo un problema al actualizar la información del docente: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request) {
        $rules = [
            'nombre' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            'ap_paterno' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            'ap_materno' => 'nullable|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            'ci' => 'required|string|regex:/^\d{7}(?:-[0-9A-Z]{1,2})?$/|unique:personas,ci|min:7',
            'genero' => 'required|in:Mujer,Hombre,Otro',
            'email' => 'required|email|unique:personas,email',
            'telefono' => 'nullable|string|regex:/^[0-9+()-]{8,15}$/|unique:num_telefonos,numero',
        ];
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $user = User::firstOrCreate(
                ['name' => $this->generateUniqueUsername($request->nombre)],
                ['email' => $request->email, 'password' => Hash::make('u.'.$request->ci)]
            );

            $user->assignRole('Docente');

            $persona = Persona::create([
                'user_id' => $user->id,
                'nombre' => $request->nombre,
                'ap_paterno' => $request->ap_paterno,
                'ap_materno' => $request->ap_materno,
                'ci' => $request->ci,
                'genero' => $request->genero,
                'email' => $request->email,
                'rol' => 'D'
            ]);

            $persona->numTelefono()->create([
                'numero' => $request->telefono,
            ]);

            $persona->docente()->create();

            return response()->json(['success' => 'La información se guardó con éxito.'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Ocurrió un error. Detalles: ' . $th->getMessage()], 500);
        }
    }

    private function generateUniqueUsername($nombre) {
        $username = strtolower($nombre);
        $numeroAleatorio = mt_rand(1000, 9999);
        return $username . $numeroAleatorio;
    }


    
    //REVISAR PARA ABAJO
    public function formInscripcion() {
        $horarios = Horario::all();
        return view('admin.usuarios.estudiantes.create', compact('horarios'));
    }

    public function inscripcion(Request $request) {
        // Reglas de validación para el estudiante
        $rules = [
            'nombre' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            'ap_pat' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            'ap_mat' => 'nullable|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            'ci' => 'required|string|regex:/^\d{7}(?:-[0-9A-Z]{1,2})?$/|unique:personas,ci|min:7',
            'genero' => 'required|in:Mujer,Hombre,Otro',
            'email' => 'required|email|unique:personas,email',
            'telefono' => 'required|string|regex:/^[0-9+()-]{8,15}$/|unique:num_telefonos,numero',
            'direccion' => 'required|string',
            'fNac' => 'required|date|before:-5 years',
            'horario' => 'required|integer|exists:horarios,id',
        ];
        $request->validate($rules);
        if ($request->filled('nombreC')) {
            // Validar los datos del contacto
            $rulesContacto = [
                'nombreC' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                'ap_patC' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                'ap_matC' => 'nullable|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                'ciC' => 'required|string|regex:/^\d{7}(?:-[0-9A-Z]{1,2})?$/|unique:personas,ci|min:7',
                'generoC' => 'required|in:Mujer,Hombre,Otro',
                'emailC' => 'nullable|email',
                'telefonoC' => 'nullable|string|regex:/^[0-9+()-]{8,15}$/|unique:num_telefonos,numero',
            ];
            $request->validate($rulesContacto);    
            if ($request->filled('telefonoC')) {
                if ($request->telefono == $request->telefonoC) {
                    return back()->with('error', 'El número de teléfono ' . $request->telefono .' del estudiante es el mismo del contacto.');
                }
            }
            $contacId = $this->saveContacto($request);
            if ($contacId === null) {
                return back()->with('error', 'Hubo un error al guardar los datos del contacto.');
            }
        }
        try {
            // Crear o recuperar el usuario
            $user = User::firstOrCreate(
                ['name' => $this->generateUniqueUsername($request->nombre)],
                ['email' => $request->email, 'password' => Hash::make('u.'.$request->ci)]
            );
            $user->assignRole('Estudiante');
    
            // Crear la persona del estudiante
            $pers = $user->persona()->create([
                'nombre' => $request->nombre,
                'ap_paterno' => $request->ap_pat,
                'ap_materno' => $request->ap_mat,
                'ci' => $request->ci,
                'genero' => $request->genero,
                'email' => $request->email,
            ]);
    
            // Agregar el número de teléfono si existe
            $telefono = $request->telefono ? ['numero' => $request->telefono] : null;
            $pers->numTelefono()->create($telefono);
    
            // Crear la información del estudiante
            if ($request->filled('nombreC')) {
                $pers->estudiante()->create([
                    'direccion' => $request->direccion,
                    'fecha_nacimiento' => $request->fNac,
                    'contact_id' => $contacId,
                    'turno_id' => $request->horario,
                ]);
            } else {
                $pers->estudiante()->create([
                    'direccion' => $request->direccion,
                    'fecha_nacimiento' => $request->fNac,
                    'turno_id' => $request->horario,
                ]);
            }
    
            return redirect()->route('admin.E.show', $pers->id);
        } catch (\Throwable $th) {
            return back()->with('error', 'Hubo un error durante la inscripción. Por favor, inténtalo de nuevo. Detalles: ' . $th->getMessage());
        }
    }

    public function saveContacto(Request $request) {
        try {
            $contacto = Persona::create([
                'nombre' => $request->nombreC,
                'ap_paterno' => $request->ap_patC,
                'ap_materno' => $request->ap_matC,
                'ci' => $request->ciC,
                'genero' => $request->generoC,
                'email' => $request->emailC,
                'rol' => 'F',
            ]);

            $telefono = ['numero' => $request->telefonoC];
            $contacto->numTelefono()->create($telefono);

            $contac = $contacto->contacto()->create();
            return $contac->id;
        } catch (\Throwable $th) {
            return back()->with('error', 'Hubo un error en los datos del contacto. Por favor, inténtalo de nuevo. Detalles: ' . $th->getMessage());
        }
    }
    

    
    public function update(Request $request, $id) {
        $estud = Estudiante::find($id);
            if ($estud) {
                $rules = [
                    'nombre' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                    'ap_pat' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                    'ap_mat' => 'nullable|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                    'ci' => 'required|string|regex:/^\d{7}(?:-[0-9A-Z]{1,2})?$/|min:7|unique:personas,ci,' . $estud->persona->id,
                    'genero' => 'required|in:Mujer,Hombre,Otro',
                    'email' => 'required|email|unique:personas,email,' . $estud->persona->id,
                    'direccion' => 'required|string',
                    'telefono' => 'nullable|string|regex:/^[0-9+()-]{8,15}$/|unique:num_telefonos,numero,' . $estud->persona->numTelefono->id,
                    'fnac' => 'required|date|before:-5 years',
                    'horario' => 'required|numeric|exists:horarios,id',
                ];
                $request->validate($rules);
            } else {
                return back()->with('error', 'Estudiante no encontrado');
            }
        try {
            $estud->direccion = $request->direccion;
            $estud->fecha_nacimiento = $request->fnac;
            $estud->turno_id = $request->horario;
            $estud->update();
            $pers = Persona::find($estud->persona->id);
            $pers->nombre = $request->nombre;
            $pers->ap_paterno = $request->ap_pat;
            $pers->ap_materno = $request->ap_mat;
            $pers->ci = $request->ci;
            $pers->genero = $request->genero;
            $pers->email = $request->email;
            $pers->update();
            NumTelefono::where('id_persona', $pers->id)->update(['numero' => $request->telefono]);
    
            return back()->with('success', 'La informacion se actualizo con éxito.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Ocurrio un error: '.$th->getMessage());
        }
    }
}
