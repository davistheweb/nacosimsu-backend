<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'about' => ['required', 'string'],

            'image' => ['nullable', 'image', 'max:5120'],

            'location' => ['required', 'string', 'max:255'],

            'date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'time' => [
                'required',
                'date_format:H:i',
            ],

            'event_type' => [
                'required',
                'in:virtual,physical'
            ],

            'presented_by' => [
                'required',
                'string',
                'max:255'
            ],

            'hosted_by' => [
                'required',
                'string',
                'max:255'
            ],
            'status' => [
                'sometimes',
                'in:draft,published,cancelled,completed',
            ],

            'host_contact' => [
                'required',
                'string',
                'max:255'
            ],
        ];
    }
}