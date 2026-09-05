<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'publication_year' => 'integer',
            'views_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByType($query, ?string $type)
    {
        if ($type && in_array($type, ['book', 'podcast', 'academic'])) {
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
        if (!$this->media_embed_url) {
            return null;
        }

        $url = trim($this->media_embed_url);

        // YouTube Embed
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $url, $matches)) {
            $videoId = $matches[1];
            return '<iframe width="100%" height="315" src="https://www.youtube.com/embed/' . $videoId . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="border-radius: var(--radius-md);"></iframe>';
        }

        // Spotify Embed
        if (str_contains($url, 'spotify.com')) {
            if (!str_contains($url, '/embed/')) {
                $spotifyUrl = preg_replace('/spotify\.com\/(episode|track|show)\//', 'spotify.com/embed/$1/', $url);
            } else {
                $spotifyUrl = $url;
            }
            return '<iframe style="border-radius: 12px;" src="' . htmlspecialchars($spotifyUrl) . '" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>';
        }

        return null;
    }
}
