<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class EventRegistration extends Model
{
    protected $fillable = [
        'event_id',
        'full_name',
        'phone',
        'email',
        'department',
        'level',
    ];
    public function store(Request $request, Event $event)
    {

        $request->validate([
            'full_name' => ['required'],
            'email' => ['required', 'email'],
            'phone' => ['required'],
            'department' => ['required'],
            'level' => ['required']
        ]);

        $registration = $event->registrations()
            ->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => $registration
        ]);
    }
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
