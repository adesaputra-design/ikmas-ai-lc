<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminEventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::latest('event_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('speaker_name', 'like', "%{$search}%")
                  ->orWhere('topic', 'like', "%{$search}%");
            });
        }

        $events = $query->paginate(15)->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'topic' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'event_date' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15'],
            'location_url' => ['nullable', 'url', 'max:255'],
            'speaker_name' => ['required', 'string', 'max:255'],
            'speaker_title' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:upcoming,completed'],
            'recording_url' => ['nullable', 'url', 'max:255'],
            'materials_url' => ['nullable', 'string', 'max:255'],
        ]);

        $slugBase = Str::slug($validated['title']);
        $slug = $slugBase;
        $counter = 1;
        while (Event::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter++;
        }

        Event::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'topic' => $validated['topic'],
            'description' => $validated['description'],
            'event_date' => $validated['event_date'],
            'duration_minutes' => $validated['duration_minutes'],
            'location_url' => $validated['location_url'] ?? null,
            'speaker_name' => $validated['speaker_name'],
            'speaker_title' => $validated['speaker_title'] ?? null,
            'status' => $validated['status'],
            'recording_url' => $validated['recording_url'] ?? null,
            'materials_url' => $validated['materials_url'] ?? null,
        ]);

        return redirect()->route('admin.agenda.index')->with('success', 'Agenda kegiatan berhasil dijadwalkan!');
    }

    public function edit(Event $agenda)
    {
        return view('admin.events.edit', compact('agenda'));
    }

    public function update(Request $request, Event $agenda)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'topic' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'event_date' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15'],
            'location_url' => ['nullable', 'url', 'max:255'],
            'speaker_name' => ['required', 'string', 'max:255'],
            'speaker_title' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:upcoming,completed'],
            'recording_url' => ['nullable', 'url', 'max:255'],
            'materials_url' => ['nullable', 'string', 'max:255'],
        ]);

        $agenda->update($validated);

        return redirect()->route('admin.agenda.index')->with('success', 'Agenda kegiatan berhasil diperbarui!');
    }

    public function destroy(Event $agenda)
    {
        $title = $agenda->title;
        $agenda->delete();

        return redirect()->route('admin.agenda.index')->with('success', "Agenda \"{$title}\" berhasil dihapus!");
    }

    public function getBroadcastText(Event $agenda)
    {
        $formattedDate = $agenda->formatted_date;
        $formattedTime = $agenda->formatted_time;
        $webUrl = url('/agenda/' . $agenda->slug);
        $location = $agenda->location_url ?: 'Tautan virtual akan dibagikan di grup menjelang sesi.';
        $speakerTitle = $agenda->speaker_title ? "({$agenda->speaker_title})" : "";

        $text = "📢 *[IKMAS AI LEARNING CENTER] — UNDANGAN SESI BELAJAR* 🚀\n\n"
              . "Assalamu'alaikum Wr. Wb. Rekan-rekan Alumni Assalaam,\n"
              . "Mari bergabung dalam agenda belajar bersama komunitas:\n\n"
              . "📌 *Topik:* {$agenda->title}\n"
              . "🏷 *Kategori:* {$agenda->topic}\n"
              . "🗓 *Hari/Tanggal:* {$formattedDate}\n"
              . "⏰ *Waktu:* {$formattedTime} WIB (~{$agenda->duration_minutes} Menit)\n"
              . "🎙 *Narasumber:* {$agenda->speaker_name} {$speakerTitle}\n"
              . "📍 *Ruang Virtual:* {$location}\n\n"
              . "📖 *Tentang Sesi:* \n"
              . Str::limit($agenda->description, 200) . "\n\n"
              . "Ajak rekan alumni lainnya untuk belajar AI dan bertumbuh bersama!\n"
              . "🌐 Detail & RSVP: {$webUrl}\n\n"
              . "_Salam Hangat, Pengurus IKMAS AI Learning Center_";

        return response()->json([
            'broadcast_text' => $text,
        ]);
    }
}
