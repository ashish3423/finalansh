<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VehicleExpiryReminderService
{
    public function forUser(int $userId): Collection
    {
        $today = Carbon::now('Asia/Kolkata')->startOfDay();
        $remindThrough = $today->copy()->addDays(7)->toDateString();
        $expiryFields = [
            'insurance' => ['column' => 'insurance_expiry', 'label' => 'Insurance'],
            'permit' => ['column' => 'permit_expiry', 'label' => 'Permit'],
            'fitness' => ['column' => 'fitness_expiry', 'label' => 'Fitness'],
        ];

        $vehicles = DB::table('vehicles')
            ->where('status', '<>', 'inactive')
            ->where(function ($query) use ($expiryFields, $remindThrough): void {
                foreach ($expiryFields as $field) {
                    $query->orWhereDate($field['column'], '<=', $remindThrough);
                }
            })
            ->get(['id', 'registration_number', 'insurance_expiry', 'permit_expiry', 'fitness_expiry']);

        $newReminders = [];
        foreach ($vehicles as $vehicle) {
            foreach ($expiryFields as $type => $field) {
                $expiryDate = $vehicle->{$field['column']};
                if (! $expiryDate || $expiryDate > $remindThrough) {
                    continue;
                }

                $newReminders[] = [
                    'user_id' => $userId,
                    'vehicle_id' => $vehicle->id,
                    'expiry_type' => $type,
                    'expiry_date' => $expiryDate,
                    'remind_on' => $today->toDateString(),
                    'closed_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if ($newReminders) {
            DB::table('vehicle_expiry_reminders')->insertOrIgnore($newReminders);
        }

        $reminders = DB::table('vehicle_expiry_reminders as reminder')
            ->join('vehicles as vehicle', 'vehicle.id', '=', 'reminder.vehicle_id')
            ->where('reminder.user_id', $userId)
            ->whereNull('reminder.closed_at')
            ->where(function ($query) use ($today): void {
                $query->whereNull('reminder.remind_on')
                    ->orWhereDate('reminder.remind_on', '<=', $today->toDateString());
            })
            ->where('reminder.expiry_date', '<=', $remindThrough)
            ->where(function ($query) use ($expiryFields): void {
                foreach ($expiryFields as $type => $field) {
                    $query->orWhere(function ($condition) use ($type, $field): void {
                        $condition->where('reminder.expiry_type', $type)
                            ->whereColumn('reminder.expiry_date', 'vehicle.'.$field['column']);
                    });
                }
            })
            ->orderBy('reminder.expiry_date')
            ->orderBy('vehicle.registration_number')
            ->get([
                'reminder.id as reminder_id',
                'vehicle.registration_number',
                'reminder.expiry_type',
                'reminder.expiry_date',
            ]);

        return $reminders->map(function (object $reminder) use ($today, $expiryFields): object {
            $expiryDate = Carbon::parse($reminder->expiry_date, 'Asia/Kolkata')->startOfDay();
            $reminder->days_remaining = (int) $today->diffInDays($expiryDate, false);
            $reminder->expiry_label = $expiryFields[$reminder->expiry_type]['label'];
            $reminder->formatted_expiry_date = $expiryDate->format('d M Y');

            return $reminder;
        });
    }
}
