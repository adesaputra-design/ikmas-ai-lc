<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $upcomingEvents = Event::where('status', 'upcoming')
            ->orderBy('event_date', 'asc')
            ->get();

        $pastEvents = Event::where('status', 'completed')
            ->orderBy('event_date', 'desc')
            ->paginate(6);

        return view('events.index', compact('upcomingEvents', 'pastEvents'));
    }

    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        return view('events.show', compact('event'));
    }
}
