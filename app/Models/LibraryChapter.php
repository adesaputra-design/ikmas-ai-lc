<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LibraryChapter extends Model
{
    public const WORDS_PER_MINUTE = 200;

    protected $fillable = [
        'title',
        'subtitle',
        'summary_points',
        'body',
        'research_box',
        'action_title',
        'action_checklist',
    ];

    protected function casts(): array
    {
        return [
            'summary_points' => 'array',
            'action_checklist' => 'array',
            'position' => 'integer',
            'reading_minutes' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (LibraryChapter $chapter) {
            $chapter->position ??= (int) static::where('library_item_id', $chapter->library_item_id)->max('position') + 1;
            $chapter->slug ??= $chapter->uniqueSlug();
        });

        static::saving(function (LibraryChapter $chapter) {
            $chapter->reading_minutes = $chapter->calculateReadingMinutes();
        });

        static::saved(fn (LibraryChapter $chapter) => $chapter->item->syncReadingTime());
        static::deleted(fn (LibraryChapter $chapter) => $chapter->item->syncReadingTime());
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(LibraryItem::class, 'library_item_id');
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

        $words = count(preg_split('/\s+/u', trim(strip_tags($text)), -1, PREG_SPLIT_NO_EMPTY));

        return max(1, (int) ceil($words / self::WORDS_PER_MINUTE));
    }

    public function getBodyHtmlAttribute(): string
    {
        return self::renderMarkdown($this->body);
    }

    public function getResearchHtmlAttribute(): ?string
    {
        return $this->research_box ? self::renderMarkdown($this->research_box) : null;
    }

    public static function renderMarkdown(string $markdown): string
    {
        return Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    private function uniqueSlug(): string
    {
        $base = Str::slug($this->title) ?: 'bab';
        $slug = $base;
        $counter = 2;

        while (static::where('library_item_id', $this->library_item_id)->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
