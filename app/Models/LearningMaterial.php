<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'level',
        'pillar',
        'summary',
        'content',
        'reading_minutes',
        'video_url',
        'slide_url',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'reading_minutes' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getLevelLabelAttribute(): string
    {
        return match ($this->level) {
            'beginner' => 'Beginner',
            'explorer' => 'Explorer',
            'practitioner' => 'Practitioner',
            default => ucfirst($this->level),
        };
    }

    public function getLevelColorAttribute(): string
    {
        return match ($this->level) {
            'beginner' => 'emerald',
            'explorer' => 'cyan',
            'practitioner' => 'amber',
            default => 'primary',
        };
    }

    public function getPillarLabelAttribute(): string
    {
        return match ($this->pillar) {
            'basics' => 'AI Basics',
            'tools' => 'AI Tools',
            'productivity' => 'AI Productivity',
            'workflow' => 'AI Workflow',
            'opportunity' => 'AI for Opportunity',
            default => ucfirst($this->pillar),
        };
    }
}
