<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ordonnance;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Récupère les messages d'une ordonnance.
     */
    public function index(Ordonnance $ordonnance, Request $request)
    {
        // Vérification des droits d'accès
        $user = $request->user();
        if ($ordonnance->client_id !== $user->id && $ordonnance->pharmacien_id !== $user->id) {
            abort(403);
        }

        $messages = Message::where('ordonnance_id', $ordonnance->id)
            ->with(['sender:id,name', 'receiver:id,name'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer les messages non lus comme lus si l'utilisateur courant est le receveur
        Message::where('ordonnance_id', $ordonnance->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    /**
     * Envoie un nouveau message.
     */
    public function store(Ordonnance $ordonnance, Request $request)
    {
        $user = $request->user();
        
        // Seuls le client ou le pharmacien assigné peuvent envoyer des messages
        if ($ordonnance->client_id !== $user->id && $ordonnance->pharmacien_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $receiverId = ($user->id === $ordonnance->client_id) ? $ordonnance->pharmacien_id : $ordonnance->client_id;

        // On ne peut pas chatter s'il n'y a pas encore de pharmacien assigné
        if (!$receiverId) {
            return response()->json(['error' => 'Aucun destinataire disponible.'], 422);
        }

        $message = Message::create([
            'ordonnance_id' => $ordonnance->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'content' => $validated['content'],
        ]);

        return response()->json($message->load(['sender:id,name', 'receiver:id,name']));
    }
}
