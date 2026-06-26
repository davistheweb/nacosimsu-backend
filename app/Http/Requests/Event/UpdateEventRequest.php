<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'name' => ['sometimes', 'string', 'max:255'],

            'about' => ['sometimes', 'string'],

            'image' => ['nullable', 'image', 'max:5120'],

            'location' => ['sometimes', 'string'],

            'date' => [
                'sometimes',
                'date',
            ],

            'time' => [
                'sometimes',
                'date_format:H:i',
            ],

            'event_type' => [
                'sometimes',
                'in:virtual,physical'
            ],

            'presented_by' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'hosted_by' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'host_contact' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'status' => [
                'sometimes',
                'in:draft,published,cancelled,completed'
            ],
        ];
    }
}