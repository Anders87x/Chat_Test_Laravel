<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Events\UserStatusChanged;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all(); // Obtiene todos los usuarios
        return view('dashboard', compact('users')); // Devuelve la vista con los usuarios
    }

    public function changeStatus(User $user, bool $isOnline)
    {
        $user = User::findOrFail($userId);
        $isOnline = $request->input('is_online');
        $user->is_online = $isOnline;
        $user->save();

        // Emitir el evento de estado de usuario actualizado
        broadcast(new UserStatusChanged($user))->toOthers();

        return response()->json(['message' => 'Estado de usuario actualizado']);
    }
}
