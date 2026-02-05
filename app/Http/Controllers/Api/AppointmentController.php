<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentService $appointments)
    {
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'service_id' => ['required', 'exists:services,id'],
            'service_variant_id' => ['nullable', 'exists:service_variants,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'start_at' => ['required', 'string'],
            'date' => ['nullable', 'date'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'evc' => ['nullable', 'string', 'max:20'],
            'vehicle_model' => ['nullable', 'string', 'max:255'],
        ]);

        $appointment = $this->appointments->book([
            ...$validated,
            'user_id' => $request->user()?->id,
        ]);

        return response()->json($appointment->fresh(['service', 'serviceVariant', 'employee']), 201);
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['service', 'serviceVariant', 'employee', 'profile']);

        return response()->json($appointment);
    }

    public function destroy(Appointment $appointment, Request $request)
    {
        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->input('reason'),
            'cancelled_by' => $request->user()?->id ? 'user:'.$request->user()->id : 'guest',
        ]);

        return response()->json([
            'message' => 'Appointment cancelled',
            'appointment' => $appointment->fresh(),
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,confirmed,cancelled,completed'],
        ]);

        $appointment->update([
            'status' => $validated['status'],
            'cancelled_by' => $validated['status'] === 'cancelled' && $request->user()?->id
                ? 'user:'.$request->user()->id
                : $appointment->cancelled_by,
        ]);

        return response()->json([
            'message' => 'Appointment updated',
            'appointment' => $appointment->fresh(),
        ]);
    }
}
