<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class LibraryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'type',
        'category',
        'summary_preview',
        'content',
        'author_name',
        'reading_time',
        'podcast_source',
        'media_embed_url',
        'duration',
        'academic_degree',
        'institution',
        'publication_year',
        'co_authors',
        'external_url',
        'file_path',
        'cover_image',
        'status',
        'rejection_note',
        'is_featured',
        'views_count',
        'series_name',
        'edition_number',
        'is_public',
        'assessment',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'publication_year' => 'integer',
            'views_count' => 'integer',
            'edition_number' => 'integer',
            'is_public' => 'boolean',
            'assessment' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class, 'bookmarkable_id')
            ->where('bookmarkable_type', 'library');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(LibraryChapter::class)->orderBy('position');
    }

    public function isEbook(): bool
    {
        return $this->type === 'ebook';
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeVisibleInCatalog(Builder $query): Builder
    {
        return $query->approved()->where(function (Builder $query): void {
            $query->where('type', '!=', 'ebook')->orWhere('is_public', true);
        });
    }

    public function isReadableBy(?User $user): bool
    {
        if (! $this->isApproved()) {
            return false;
        }

        if ($this->isEbook()) {
            return $this->is_public;
        }

        return $user && (
            $user->isAdmin() || $user->isStaff() || $user->isMember() || ($user->isSubscriber() && $user->isActive())
        );
    }

    /**
     * Recalculate an assessment from its active definition and validated answers.
     *
     * @param  array<string, int|string>  $answers
     * @return array{total: int, principleScores: array<int, array{name: string, score: float}>, range: array<string, mixed>}
     */
    public function calculateAssessment(array $answers): array
    {
        $definition = $this->assessment;
        if (! $this->isEbook() || ! is_array($definition)) {
            throw ValidationException::withMessages(['answers' => 'Asesmen ebook ini belum tersedia.']);
        }

        $questionIds = collect($definition['principles'] ?? [])
            ->flatMap(fn (array $principle): array => array_column($principle['questions'] ?? [], 'id'))
            ->map(fn (string $id): string => $id)
            ->all();
        $submittedIds = array_map('strval', array_keys($answers));

        if (count($submittedIds) !== count($questionIds) || array_diff($questionIds, $submittedIds) !== [] || array_diff($submittedIds, $questionIds) !== []) {
            throw ValidationException::withMessages(['answers' => 'Jawab semua pertanyaan asesmen yang berlaku.']);
        }

        $scaleMin = filter_var($definition['scale']['min'] ?? null, FILTER_VALIDATE_INT);
        $scaleMax = filter_var($definition['scale']['max'] ?? null, FILTER_VALIDATE_INT);
        if ($scaleMin === false || $scaleMax === false || $scaleMax <= $scaleMin) {
            throw ValidationException::withMessages(['answers' => 'Skala asesmen tidak valid.']);
        }

        $normalizedAnswers = [];
        $total = 0;
        $principleScores = [];

        foreach ($definition['principles'] as $principle) {
            $principleTotal = 0;
            foreach ($principle['questions'] as $question) {
                $questionId = (string) $question['id'];
                $value = filter_var($answers[$questionId] ?? null, FILTER_VALIDATE_INT);
                if ($value === false || $value < $scaleMin || $value > $scaleMax) {
                    throw ValidationException::withMessages(['answers' => 'Setiap jawaban harus berada dalam rentang skala yang ditentukan.']);
                }

                $normalizedAnswers[$questionId] = $value;
                $principleTotal += $value;
            }

            $total += $principleTotal;
            $principleScores[] = [
                'name' => $principle['name'],
                'score' => round($principleTotal / count($principle['questions']), 2),
            ];
        }

        $range = collect($definition['ranges'] ?? [])->first(
            fn (array $candidate): bool => $total >= (int) $candidate['min'] && $total <= (int) $candidate['max']
        );
        if (! $range) {
            throw ValidationException::withMessages(['answers' => 'Rentang hasil asesmen belum mencakup skor ini.']);
        }

        return [
            'total' => $total,
            'answers' => $normalizedAnswers,
            'principleScores' => $principleScores,
            'range' => $range,
        ];
    }

    public function scopeByType($query, ?string $type)
    {
        if ($type && in_array($type, ['book', 'podcast', 'academic', 'ebook'])) {
            return $query->where('type', $type);
        }

        return $query;
    }

    public function scopeByCategory($query, ?string $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }

        return $query;
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Type helpers
    public function isBook(): bool
    {
        return $this->type === 'book';
    }

    public function isPodcast(): bool
    {
        return $this->type === 'podcast';
    }

    public function isAcademic(): bool
    {
        return $this->type === 'academic';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // Badges & Labels
    public function getTypeBadgeAttribute(): array
    {
        return match ($this->type) {
            'book' => [
                'label' => 'Rangkuman Buku',
                'icon' => '📚',
                'class' => 'badge-primary',
            ],
            'podcast' => [
                'label' => 'Resume Podcast',
                'icon' => '🎙️',
                'class' => 'badge-cyan',
            ],
            'academic' => [
                'label' => 'Karya Ilmiah Alumni',
                'icon' => '🎓',
                'class' => 'badge-amber',
            ],
            'ebook' => [
                'label' => 'Ebook',
                'icon' => '📖',
                'class' => 'badge-emerald',
            ],
            default => [
                'label' => 'Pustaka AI',
                'icon' => '📖',
                'class' => 'badge-secondary',
            ],
        };
    }

    public function getDegreeLabelAttribute(): string
    {
        return match ($this->academic_degree) {
            'skripsi' => 'Skripsi (S1)',
            'tesis' => 'Tesis (S2)',
            'disertasi' => 'Disertasi (S3)',
            'jurnal' => 'Jurnal Ilmiah',
            default => 'Karya Ilmiah',
        };
    }

    public function getEmbedHtmlAttribute(): ?string
    {
        if (! $this->media_embed_url) {
            return null;
        }

        $url = trim($this->media_embed_url);

        // YouTube Embed
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $url, $matches)) {
            $videoId = $matches[1];

            return '<iframe width="100%" height="315" src="https://www.youtube.com/embed/'.$videoId.'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="border-radius: var(--radius-md);"></iframe>';
        }

        // Spotify Embed
        if (str_contains($url, 'spotify.com')) {
            if (! str_contains($url, '/embed/')) {
                $spotifyUrl = preg_replace('/spotify\.com\/(episode|track|show)\//', 'spotify.com/embed/$1/', $url);
            } else {
                $spotifyUrl = $url;
            }

            return '<iframe style="border-radius: 12px;" src="'.htmlspecialchars($spotifyUrl).'" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>';
        }

        return null;
    }
}
