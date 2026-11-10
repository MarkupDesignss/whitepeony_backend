<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->paginate(10);
        // dd($events);
        return view('admin.events.index', compact('events'));
    }

    // Show form to create an event
    public function create()
    {
        return view('admin.events.create');
    }

    // Store new event
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'address' => 'required|string|max:255',
            'event_date' => 'required|date',
            'capacity' => 'required|integer|min:1',
            'venue_map_link' => 'nullable|string',
            'price_per_seat' => 'required|integer|min:1',
            'speaker' => 'required',
            'lat' => 'nullable|string',
            'long' => 'nullable|string',
            'agenda' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
        } else {
            $imagePath = $event->image ?? null;
        }

        Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'address' => $request->address,
            'event_date' => $request->event_date,
            'capacity' => $request->capacity,
            'venue_map_link' => $request->venue_map_link ?? null,
            'price_per_seat' => $request->price_per_seat,
            'speaker' => $request->speaker,
            'lat' => $request->lat ?? null,
            'long' => $request->long ?? null,
            'agenda' => $request->agenda,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
    }

    // Show single event
    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }

    // Show edit form
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    // Update event
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'address' => 'required|string|max:255',
            'event_date' => 'required|date',
            'capacity' => 'required|integer|min:1',
            'venue_map_link' => 'nullable|string',
            'price_per_seat' => 'required|integer|min:1',
            'speaker' => 'required',
            'lat' => 'nullable|string',
            'long' => 'nullable|string',
            'agenda' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->only(['title', 'description', 'address', 'event_date', 'capacity', 'venue_map_link', 'price_per_seat', 'speaker', 'lat', 'long', 'agenda']);

        // ✅ If empty or missing -> Set to NULL
        $data['lat'] = $request->filled('lat') ? $request->lat : null;
        $data['long'] = $request->filled('long') ? $request->long : null;
        $data['venue_map_link'] = $request->filled('venue_map_link') ? $request->venue_map_link : null;

        // ✅ Handle image update
        if ($request->hasFile('image')) {
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }

            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    // Delete event
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
    }

    public function registeredEvents(Request $request)
    {
        $registrations = DB::table('event_registrations')
            ->join('event_attendees', 'event_attendees.registration_id', '=', 'event_registrations.id')
            ->where('event_registrations.status', 'registered')
            ->select(
                'event_attendees.id as attendee_id',
                'event_attendees.registration_id',
                'event_attendees.email as attendee_email',
                'event_registrations.id as registration_id',
                'event_registrations.event_id',
                'event_registrations.customer_id',
                'event_registrations.full_name',
                'event_registrations.status',
                'event_registrations.per_seat_price',
                'event_registrations.total_price',
                'event_registrations.payment_status',
                'event_registrations.booked_seats',
                'event_registrations.created_at',
            )
            ->orderBy('event_registrations.created_at', 'desc')
            ->paginate(5)
            ->appends($request->query()); // Keeps filters/search (if any)
        return view('admin.events.registeredEvent', compact('registrations'));
    }

}