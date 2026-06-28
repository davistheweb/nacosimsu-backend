<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Http;
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

    // public function uploadImage($request): ?string
    // {
    //     if (!$request->hasFile('image')) {
    //         return null;
    //     }

    //     return $request
    //         ->file('image')
    //         ->store('events', 'public');
    // }

    // public function uploadImage($request): ?string
    // {
    //     if (!$request->hasFile('image')) {
    //         return null;
    //     }

    //     $path = $request->file('image')->store('events', 'public');

    //     return dd([
    //         'disk' => config('filesystems.disks.public'),
    //         'storage_exists' => Storage::disk('public')->exists($path),
    //         'all_files' => Storage::disk('public')->allFiles(),
    //         'events_files' => Storage::disk('public')->files('events'),
    //     ]);
    // }

    public function uploadImage($request): ?array
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');

        $response = Http::withToken(config('services.pxxl.api_key'))
            ->attach(
                'file',
                fopen($file->getRealPath(), 'r'),
                $file->getClientOriginalName()
            )
            ->post(
                config('services.pxxl.cdn_endpoint'),
                [
                    'visibility' => 'public',
                ]
            );

        if (!$response->successful()) {
            throw new \Exception(
                'Image upload failed: ' . $response->body()
            );
        }

        $asset = $response->json('asset');

        return [
            'url' => $asset['publicUrl'],
            'key' => $asset['key'],
            'id' => $asset['id'],
        ];
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