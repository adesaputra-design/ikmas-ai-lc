<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\LearningMaterial;
use App\Models\Prompt;
use App\Models\Showcase;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HermesController extends Controller
{
    private const LIMIT = 5;

    public function status(Request $request): JsonResponse
    {
        if ($failed = $this->authorizeHermes($request)) {
            return $failed;
        }

        $databaseOk = true;
        try {
            DB::connection()->getPdo();
        } catch (\Throwable) {
            $databaseOk = false;
        }

        return response()->json([
            'ok' => $databaseOk,
            'service' => 'ikmas-ai-lc',
            'environment' => app()->environment(),
            'database' => $databaseOk ? 'ok' : 'error',
            'cache' => config('cache.default'),
            'checked_at' => now()->toIso8601String(),
        ], $databaseOk ? 200 : 503);
    }

    public function latest(Request $request): JsonResponse
    {
        if ($failed = $this->authorizeHermes($request)) {
            return $failed;
        }

        $materials = LearningMaterial::query()
            ->latest()
            ->limit(self::LIMIT)
            ->get(['id', 'title', 'slug', 'is_published', 'created_at']);

        $showcases = Showcase::query()
            ->with('user:id,name')
            ->latest()
            ->limit(self::LIMIT)
            ->get(['id', 'user_id', 'title', 'slug', 'status', 'created_at']);

        $events = Event::query()
            ->latest('event_date')
            ->limit(self::LIMIT)
            ->get(['id', 'title', 'slug', 'status', 'event_date', 'speaker_name']);

        return response()->json([
            'materials' => $materials->map(fn (LearningMaterial $material) => [
                'id' => $material->id,
                'title' => $material->title,
                'slug' => $material->slug,
                'published' => $material->is_published,
                'created_at' => optional($material->created_at)->toIso8601String(),
            ]),
            'showcases' => $showcases->map(fn (Showcase $showcase) => [
                'id' => $showcase->id,
                'title' => $showcase->title,
                'slug' => $showcase->slug,
                'status' => $showcase->status,
                'member' => $showcase->user?->name,
                'created_at' => optional($showcase->created_at)->toIso8601String(),
            ]),
            'events' => $events->map(fn (Event $event) => [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'status' => $event->status,
                'speaker' => $event->speaker_name,
                'event_date' => optional($event->event_date)->toIso8601String(),
            ]),
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        if ($failed = $this->authorizeHermes($request)) {
            return $failed;
        }

        return response()->json([
            'members' => User::where('role', 'member')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'pending_members' => 0,
            'materials' => [
                'total' => LearningMaterial::count(),
                'published' => LearningMaterial::where('is_published', true)->count(),
                'draft' => LearningMaterial::where('is_published', false)->count(),
            ],
            'prompts' => Prompt::count(),
            'showcases' => [
                'total' => Showcase::count(),
                'pending' => Showcase::where('status', 'pending')->count(),
                'approved' => Showcase::where('status', 'approved')->count(),
                'rejected' => Showcase::where('status', 'rejected')->count(),
            ],
            'events' => [
                'total' => Event::count(),
                'upcoming' => Event::where('status', 'upcoming')->count(),
                'completed' => Event::where('status', 'completed')->count(),
            ],
        ]);
    }

    public function pendingMembers(Request $request): JsonResponse
    {
        if ($failed = $this->authorizeHermes($request)) {
            return $failed;
        }

        $members = User::query()
            ->where('role', 'member')
            ->latest()
            ->limit(10)
            ->get(['id', 'name', 'email', 'whatsapp_number', 'alumni_year', 'created_at']);

        return response()->json([
            'pending_members' => [],
            'note' => 'Member approval belum aktif di schema. Daftar ini menampilkan member terbaru untuk review manual.',
            'recent_members' => $members->map(fn (User $member) => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'whatsapp_number' => $member->whatsapp_number,
                'alumni_year' => $member->alumni_year,
                'joined_at' => optional($member->created_at)->toIso8601String(),
            ]),
        ]);
    }

    public function eventPreview(Request $request, string $slug): JsonResponse
    {
        if ($failed = $this->authorizeHermes($request)) {
            return $failed;
        }

        $event = Event::where('slug', $slug)->firstOrFail();

        return response()->json([
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'status' => $event->status,
                'speaker' => $event->speaker_name,
                'event_date' => optional($event->event_date)->toIso8601String(),
            ],
            'broadcast_text' => $this->formatBroadcastText($event),
        ]);
    }

    private function authorizeHermes(Request $request): ?JsonResponse
    {
        $sharedSecret = config('hermes.shared_secret');
        if (! is_string($sharedSecret) || $sharedSecret === '') {
            return response()->json(['message' => 'Hermes API is not configured.'], 503);
        }

        $providedSecret = (string) $request->header('X-Hermes-Secret', '');
        if (! hash_equals($sharedSecret, $providedSecret)) {
            return response()->json(['message' => 'Unauthorized Hermes request.'], 401);
        }

        $allowedActors = config('hermes.allowed_actors', []);
        if ($allowedActors !== []) {
            $actor = (string) $request->header('X-Hermes-Actor', '');
            if (! in_array($actor, $allowedActors, true)) {
                return response()->json(['message' => 'Hermes actor is not allowed.'], 403);
            }
        }

        return null;
    }

    private function formatBroadcastText(Event $event): string
    {
        $date = $event->event_date?->translatedFormat('l, d F Y') ?? '-';
        $time = $event->event_date?->format('H:i') ?? '-';
        $speakerTitle = $event->speaker_title ? " ({$event->speaker_title})" : '';
        $location = $event->location_url ?: 'Tautan virtual akan dibagikan di grup menjelang sesi.';
        $webUrl = url('/agenda/' . $event->slug);
        $topic = $event->getAttribute('topic') ?: 'Study Group IKMAS AI';

        return "📢 *[IKMAS AI LEARNING CENTER] — UNDANGAN SESI BELAJAR* 🚀\n\n"
            . "Assalamu'alaikum Wr. Wb. Rekan-rekan Alumni Assalaam,\n"
            . "Mari bergabung dalam agenda belajar bersama komunitas:\n\n"
            . "📌 *Topik:* {$event->title}\n"
            . "🏷 *Kategori:* {$topic}\n"
            . "🗓 *Hari/Tanggal:* {$date}\n"
            . "⏰ *Waktu:* {$time} WIB (~{$event->duration_minutes} Menit)\n"
            . "🎙 *Narasumber:* {$event->speaker_name}{$speakerTitle}\n"
            . "📍 *Ruang Virtual:* {$location}\n\n"
            . "📖 *Tentang Sesi:*\n"
            . Str::limit($event->description, 200) . "\n\n"
            . "Detail & RSVP: {$webUrl}\n\n"
            . "_Salam Hangat, Pengurus IKMAS AI Learning Center_";
    }
}
