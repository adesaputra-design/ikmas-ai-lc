<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LibraryChapter extends Model
{
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
            $words = preg_match_all('/[\p{L}\p{N}]+/u', $chapter->body ?? '');
            $chapter->reading_minutes = max(1, (int) ceil($words / 200));
        });
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
}
