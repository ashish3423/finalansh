@if ($vehicleExpiryReminders->isNotEmpty())
    <div class="modal fade" id="vehicleExpiryReminderModal" tabindex="-1" aria-labelledby="vehicleExpiryReminderTitle" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="eyebrow">FLEET COMPLIANCE</div>
                        <h5 class="modal-title" id="vehicleExpiryReminderTitle">Vehicle document reminders</h5>
                    </div>
                </div>
                <div class="modal-body">
                    <p class="text-muted">These vehicle documents are expired or will expire within the next 7 days. The footer actions apply to all {{ $vehicleExpiryReminders->count() }} reminders listed here.</p>
                    <div class="table-responsive">
                        <table class="table align-middle enterprise-table mb-0">
                            <thead><tr><th>Vehicle</th><th>Document</th><th>Expiry date</th><th>Reminder</th></tr></thead>
                            <tbody>
                                @foreach ($vehicleExpiryReminders as $reminder)
                                    <tr>
                                        <td><span class="expiry-reminder-registration"><i class="bi bi-truck-front me-2"></i>{{ $reminder->registration_number }}</span></td>
                                        <td>{{ $reminder->expiry_label }}</td>
                                        <td>{{ $reminder->formatted_expiry_date }}</td>
                                        <td>
                                            @if ($reminder->days_remaining < 0)
                                                <span class="expiry-reminder-countdown expired"><strong>{{ abs($reminder->days_remaining) }} day(s)</strong><small>past expiry</small></span>
                                            @elseif ($reminder->days_remaining === 0)
                                                <span class="expiry-reminder-countdown today"><strong>Today</strong><small>expires now</small></span>
                                            @else
                                                <span class="expiry-reminder-countdown upcoming"><strong>{{ $reminder->days_remaining }} day(s)</strong><small>remaining</small></span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer expiry-reminder-footer">
                    <small class="text-muted text-end w-100">Remind again tomorrow or close all listed expiry reminders permanently.</small>
                    <div class="expiry-reminder-actions">
                    <form method="POST" action="{{ route('vehicle-expiry-reminders.remind-tomorrow') }}">
                        @csrf
                        <button class="btn btn-primary text-nowrap" type="submit"><i class="bi bi-bell me-1"></i>Remind me again</button>
                    </form>
                    <form method="POST" action="{{ route('vehicle-expiry-reminders.close') }}">
                        @csrf
                        <button class="btn btn-outline-danger text-nowrap" type="submit"><i class="bi bi-x-circle me-1"></i>Close permanently</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
