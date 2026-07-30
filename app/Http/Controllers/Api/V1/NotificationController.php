<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json(['notifications' => $notifications]);
    }

    public function marquerLue(\Illuminate\Notifications\DatabaseNotification $notification, Request $request)
    {
        if ($notification->notifiable_id !== $request->user()->id) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Notification marquée comme lue']);
    }

    public function toutMarquerLues(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues']);
    }

    public function enregistrerToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|in:ios,android,web',
        ]);

        // TODO: Sauvegarder le token pour les notifications push
        // $request->user()->tokensPush()->updateOrCreate([...])

        return response()->json(['message' => 'Token enregistré']);
    }
}
