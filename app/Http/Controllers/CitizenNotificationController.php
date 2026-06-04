<?php

namespace App\Http\Controllers;

use App\Models\CitizenNotification;

class CitizenNotificationController extends Controller
{
    public function index()
    {
        $notifications = CitizenNotification::with(['citizen', 'request'])->latest()->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(CitizenNotification $notification)
    {
        $notification->update(['read_at' => now()]);

        return back()->with('success', __('app.flash.notification_read'));
    }
}
