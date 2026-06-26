<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;


class EventRegistrationController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'full_name' => ['required'],
            'email' => ['required', 'email'],
            'phone' => ['required'],
            'department' => ['required'],
            'level' => ['required'],
        ]);

        $alreadyRegistered = $event
            ->registrations()
            ->where('email', $validated['email'])
            ->exists();

        if ($alreadyRegistered) {
            return response()->json([
                'message' => 'You have already registered for this event.'
            ], 422);
        }

        $registration = $event
            ->registrations()
            ->create($validated);

        return response()->json([
            'message' => 'Registration successful',
            'data' => $registration
        ], 201);
    }
}
