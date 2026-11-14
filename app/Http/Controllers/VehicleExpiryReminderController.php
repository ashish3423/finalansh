<?php

namespace App\Http\Controllers;

use App\Services\VehicleExpiryReminderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleExpiryReminderController extends Controller
{
    public function remindMeTomorrow(Request $request, VehicleExpiryReminderService $reminderService): RedirectResponse
    {
        $userId = (int) $request->user()->id;
        $reminderIds = $reminderService->forUser($userId)->pluck('reminder_id')->all();
        if (! $reminderIds) {
            return redirect()->route('dashboard')->with('status', 'There are no vehicle reminders due right now.');
        }

        DB::table('vehicle_expiry_reminders')->whereIn('id', $reminderIds)->where('user_id', $userId)->whereNull('closed_at')->update([
            'remind_on' => now('Asia/Kolkata')->addDay()->toDateString(),
            'updated_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('status', 'The listed vehicle reminders will appear again tomorrow.');
    }

    public function closePermanently(Request $request, VehicleExpiryReminderService $reminderService): RedirectResponse
    {
        $userId = (int) $request->user()->id;
        $reminderIds = $reminderService->forUser($userId)->pluck('reminder_id')->all();
        if (! $reminderIds) {
            return redirect()->route('dashboard')->with('status', 'There are no vehicle reminders due right now.');
        }

        DB::table('vehicle_expiry_reminders')->whereIn('id', $reminderIds)->where('user_id', $userId)->whereNull('closed_at')->update([
            'closed_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('status', 'The listed vehicle expiry reminders were closed permanently.');
    }
}
