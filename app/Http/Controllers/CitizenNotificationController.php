<?php

namespace App\Http\Controllers;

use App\Models\CitizenNotification;

class CitizenNotificationController extends Controller
{
    public function index()
    {
        $query = CitizenNotification::with(['citizen', 'complaint'])->latest();
        if (auth()->user()->role === 'citizen') {
            $query->where('citizen_id', auth()->user()->citizen?->id);
        }
        $notifications = $query->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(CitizenNotification $notification)
    {
        if (auth()->user()->role === 'citizen') {
            abort_unless($notification->citizen_id === auth()->user()->citizen?->id, 403);
        }
        $notification->update(['read_at' => now()]);

        return back()->with('success', __('app.flash.notification_read'));
    }
}
