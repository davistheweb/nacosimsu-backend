<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventService
{
    public function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);

        $original = $slug;

        $count = 2;

        while (
            Event::where('slug', $slug)->exists()
        ) {

            $slug = "{$original}-{$count}";

            $count++;
        }

        return $slug;
    }

    public function uploadImage($request): ?string
    {
        if (!$request->hasFile('image')) {
            dd(
                'No file',
                $request->all(),
                $request->files->all()
            );
        }

        dd(
            'File received',
            $request->file('image')
        );

        return $request
            ->file('image')
            ->store('events', 'public');
    }

    public function replaceImage(Event $event, $request): ?string
    {
        if (!$request->hasFile('image')) {
            return $event->image;
        }

        if (
            $event->image &&
            Storage::disk('public')->exists($event->image)
        ) {
            Storage::disk('public')->delete($event->image);
        }

        return $request
            ->file('image')
            ->store('events', 'public');
    }

    public function deleteImage(Event $event): void
    {
        if (
            $event->image &&
            Storage::disk('public')->exists($event->image)
        ) {
            Storage::disk('public')->delete($event->image);
        }
    }
}