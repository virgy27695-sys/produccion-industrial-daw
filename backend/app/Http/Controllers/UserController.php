<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    // LISTAR USUARIOS
    public function index()
    {
        return User::orderBy('name')->get();
    }


    // CREAR USUARIO
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

            'role' => [
                'required',
                'in:admin,usuario',
            ],
        ]);

        $user = User::create($data);

        return response()->json($user, 201);
    }


    // MOSTRAR USUARIO
    public function show(User $user)
    {
        return $user;
    }


    // ACTUALIZAR USUARIO
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],

            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

            'role' => [
                'required',
                'in:admin,usuario',
            ],
        ]);

        // Si no se introduce contraseña nueva,
        // mantenemos la anterior.
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }


    // ELIMINAR USUARIO
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado correctamente',
        ]);
    }
}
