<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request) {
        $rules = [
            'username' => 'required|string|max:100',
            'password' => 'required|string|min:8'
        ];
    
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }
    
        // Intenta autenticar al usuario por nombre de usuario
        if (!Auth::attempt(['name' => $request->username, 'password' => $request->password])) {
            // Si la autenticación falla, devuelve un error 401
            return response()->json([
                'status' => false,
                'errors' => ['Credenciales incorrectas']
            ], 401);
        }
    
        // Si la autenticación tiene éxito, obtén el usuario autenticado
        $user = Auth::user();
    
        // Retorna una respuesta con un token de autenticación
        return response()->json([
            'status' => true,
            'message' => 'Iniciaste sesión exitosamente',
            'data' => $user,
            'token' => $user->createToken('API TOKEN')->plainTextToken
        ], 200);
    }
    
    public function logout() {
        auth()->user()->tokens()->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Cerraste session exitosamente',
        ], 200);
    }
}
