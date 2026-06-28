<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Support\Str;
use App\Models\EventRegistration;
use App\Services\EventService;

use App\Http\Requests\Event\CreateEventRequest;

use App\Http\Requests\Event\UpdateEventRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class EventController extends Controller
{

    protected EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }
    public function store(CreateEventRequest $request)
    {
        $validated = $request->validated();

        $image = $this->eventService->uploadImage($request);

        $event = Event::create([

            ...$validated,

            'slug' => $this->eventService
                ->generateUniqueSlug(
                    $validated['name']
                ),

            // 'image' => $this->eventService
            //     ->uploadImage($request),


            'image' => $image['url'],
            'image_key' => $image['key'],
            'image_id' => $image['id'],

           'status' => $validated['status'] ?? 'draft',

            'created_by' => auth()->id(),

        ]);

        return response()->json([
            'message' => 'Event created successfully',
            'data' => $event
        ], 201);
    }

    public function index()
    {
        return Event::withCount('registrations')
            ->where('status', 'published')
            ->latest()
            ->get();
    }

    public function show(Event $event)
    {
        return response()->json(

            $event->loadCount('registrations')

        );
    }

    public function update(UpdateEventRequest $request, int $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validated();

        if (
            isset($validated['name']) &&
            $validated['name'] !== $event->name
        ) {
            $validated['slug'] = $this
                ->eventService
                ->generateUniqueSlug(
                    $validated['name']
                );
        }

        $image = $this
            ->eventService
            ->replaceImage($event, $request);

        if ($image !== null) {
            $validated['image'] = $image;
        }

        $event->update($validated);

        return response()->json([
            'message' => 'Event updated successfully',
            'data' => $event->fresh()
        ]);
    }

    public function registrations(Request $request, int $id)
    {
        $event = Event::findOrFail($id);

        return response()->json(

            $event
                ->registrations()
                ->latest()
                ->paginate(
                    $request->integer('per_page', 20)
                )

        );
    }

    public function stats()
    {
        return response()->json([

            'total_events' => Event::count(),

            'published_events' => Event::whereStatus('published')->count(),

            'draft_events' => Event::whereStatus('draft')->count(),

            'cancelled_events' => Event::whereStatus('cancelled')->count(),

            'completed_events' => Event::whereStatus('completed')->count(),

            'total_registrations' => EventRegistration::count(),

        ]);
    }

    public function adminShow(int $id)
    {
        return response()->json(

            Event::query()
                ->withCount('registrations')
                ->findOrFail($id)

        );
    }

    public function adminEvents(Request $request)
    {
        $query = Event::query()
            ->withCount('registrations');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('hosted_by', 'like', "%{$search}%")
                    ->orWhere('presented_by', 'like', "%{$search}%");

            });

        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        $allowedStatuses = [
            'draft',
            'published',
            'cancelled',
            'completed',
        ];

        if (
            $request->filled('status') &&
            in_array($request->status, $allowedStatuses)
        ) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | Event Type Filter
        |--------------------------------------------------------------------------
        */

        $allowedTypes = [
            'virtual',
            'physical',
        ];

        if (
            $request->filled('event_type') &&
            in_array($request->event_type, $allowedTypes)
        ) {
            $query->where(
                'event_type',
                $request->event_type
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $allowedSorts = [
            'created_at',
            'date',
            'name',
        ];

        $sort = $request->get('sort', 'created_at');

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        $direction = $request->get('direction', 'desc');

        $direction = $direction === 'asc'
            ? 'asc'
            : 'desc';

        $perPage = max(
            1,
            min(
                $request->integer('per_page', 10),
                100
            )
        );

        return response()->json(

            $query
                ->select([
                    'id',
                    'name',
                    'slug',
                    'image',
                    'event_type',
                    'status',
                    'date',
                    'time',
                    'location',
                    'hosted_by',
                    'presented_by',
                    'created_at',
                ])->withCount('registrations')
                ->orderBy($sort, $direction)
                ->paginate($perPage)

        );
    }

    public function destroy(int $id)
    {
        $event = Event::findOrFail($id);

        $this->eventService
            ->deleteImage($event);

        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully'
        ]);
    }
}
