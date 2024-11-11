<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivateChat; // Importa la clase PrivateChat
use App\Models\PrivateMessage; // Importa la clase PrivateMessage
use Illuminate\Support\Facades\Auth;

use App\Events\MessageSent;

class ChatController extends Controller
{
    public function getMessages($userId)
    {
        try {
            $currentUserId = auth()->id();

            // Busca la conversación entre los dos usuarios
            $chat = PrivateChat::where(function ($query) use ($currentUserId, $userId) {
                $query->where('user_one_id', $currentUserId)
                      ->where('user_two_id', $userId);
            })->orWhere(function ($query) use ($currentUserId, $userId) {
                $query->where('user_one_id', $userId)
                      ->where('user_two_id', $currentUserId);
            })->first();

            if (!$chat) {
                return response()->json(['error' => 'No se encontró la conversación'], 404);
            }

            // Carga los mensajes relacionados con el chat
            $messages = PrivateMessage::where('private_chat_id', $chat->id)
                ->with('user') // Incluye los datos del usuario
                ->get();

            return response()->json(['messages' => $messages], 200);

        } catch (\Exception $e) {
            \Log::error('Error al obtener mensajes: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al obtener los mensajes'], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'receiver_id' => 'required|exists:users,id', // ID del usuario al que se envía el mensaje
        ]);

        \Log::info('Datos recibidos:', $request->all());

        $currentUserId = auth()->id();
        $receiverId = $request->receiver_id;

        // Verifica si la conversación ya existe
        $chat = PrivateChat::where(function ($query) use ($currentUserId, $receiverId) {
            $query->where('user_one_id', $currentUserId)
                ->where('user_two_id', $receiverId);
        })->orWhere(function ($query) use ($currentUserId, $receiverId) {
            $query->where('user_one_id', $receiverId)
                ->where('user_two_id', $currentUserId);
        })->first();

        // Si no existe, crea una nueva conversación
        if (!$chat) {
            $chat = PrivateChat::create([
                'user_one_id' => $currentUserId,
                'user_two_id' => $receiverId,
            ]);
        }

        // Crea el mensaje en la tabla `private_messages`
        $message = PrivateMessage::create([
            'private_chat_id' => $chat->id,
            'user_id' => $currentUserId,
            'content' => $request->content,
        ]);

        event(new \App\Events\MessageSent($message));

        \Log::info('Evento MessageSent emitido desde el controlador', ['message_id' => $message->id]);

        return response()->json(['message' => 'Mensaje enviado con éxito', 'data' => $message]);
    }
}

