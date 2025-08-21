<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;

class ClientAppointmentController extends Controller
{
    // Get all appointments
    public function index()
    {
        $appointments = Appointment::all();
        return response()->json($appointments);
    }

    // Add a new appointment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_name' => 'required|string|max:255',
            'doctor_name' => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'reason' => 'required|string',
            'status' => 'nullable|in:Pending,Completed,Cancelled',
        ]);

        $appointment = Appointment::create($validated);
        return response()->json($appointment, 201);
    }

    // Update an appointment
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'pet_name' => 'string|max:255',
            'doctor_name' => 'string|max:255',
            'appointment_date' => 'date',
            'appointment_time' => 'string',
            'reason' => 'nullable|string',
            'status' => 'in:Pending,Completed,Cancelled',
        ]);

        $appointment->update($validated);
        return response()->json($appointment);
    }

    // Delete an appointment
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
