<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    /**
     * Marque toutes les notifications non lues de l'utilisateur comme lues.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return back();
    }

    /**
     * Marque une notification spécifique comme lue et redirige vers son lien.
     */
    public function readAndRedirect(Request $request, $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        return redirect($notification->data['url'] ?? url('/dashboard'));
    }
}
