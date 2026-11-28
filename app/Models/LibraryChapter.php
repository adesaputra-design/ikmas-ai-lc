<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LibraryChapter extends Model
{
    public const WORDS_PER_MINUTE = 200;

    protected $fillable = [
        'position',
        'slug',
        'title',
        'subtitle',
        'summary_points',
        'body',
        'approved_quotes',
        'research_box',
        'action_title',
        'action_checklist',
    ];

    protected function casts(): array
    {
        return [
            'summary_points' => 'array',
            'action_checklist' => 'array',
            'approved_quotes' => 'array',
            'reading_minutes' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $chapter): void {
            $chapter->reading_minutes = $chapter->calculateReadingMinutes();
        });

        static::saved(fn (self $chapter) => $chapter->libraryItem->syncReadingTime());
        static::deleted(fn (self $chapter) => $chapter->libraryItem->syncReadingTime());
    }

    public function libraryItem(): BelongsTo
    {
        return $this->belongsTo(LibraryItem::class);
    }

    public function getRenderedBodyAttribute(): string
    {
        return Str::markdown($this->body, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    public function calculateReadingMinutes(): int
    {
        $text = implode(' ', [
            $this->body,
            $this->research_box,
            $this->action_title,
            implode(' ', $this->summary_points ?? []),
            implode(' ', $this->action_checklist ?? []),
        ]);
        $words = preg_match_all('/[\p{L}\p{N}]+/u', strip_tags($text));

        return max(1, (int) ceil($words / self::WORDS_PER_MINUTE));
    }
}
