<?php

namespace App\Http\Controllers;

use App\Models\CommandeFournisseur;
use App\Models\CommandeMessage;
use Illuminate\Http\Request;

class CommandeMessageController extends Controller
{
    /**
     * Récupère les messages d'une commande.
     */
    public function index(CommandeFournisseur $commande, Request $request)
    {
        $user = $request->user();

        // Seuls le pharmacien ou le fournisseur assigné peuvent voir les messages
        if ($commande->pharmacien_id !== $user->id && $commande->fournisseur_id !== $user->id) {
            abort(403);
        }

        $messages = CommandeMessage::where('commande_id', $commande->id)
            ->with(['sender:id,name', 'receiver:id,name'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer lus
        CommandeMessage::where('commande_id', $commande->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    /**
     * Envoie un nouveau message.
     */
    public function store(CommandeFournisseur $commande, Request $request)
    {
        $user = $request->user();

        if ($commande->pharmacien_id !== $user->id && $commande->fournisseur_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $receiverId = ($user->id === $commande->pharmacien_id) ? $commande->fournisseur_id : $commande->pharmacien_id;

        if (!$receiverId) {
            return response()->json(['error' => 'Destinataire introuvable.'], 422);
        }

        $message = CommandeMessage::create([
            'commande_id' => $commande->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'content' => $validated['content'],
        ]);

        return response()->json($message->load(['sender:id,name', 'receiver:id,name']));
    }
}
