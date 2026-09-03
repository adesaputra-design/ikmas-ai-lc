<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Showcase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'tools_used',
        'image_url',
        'project_url',
        'impact_story',
        'status',
        'is_featured',
        'admin_notes',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Kurasi',
            'approved' => 'Disetujui',
            'rejected' => 'Perlu Revisi',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'amber',
            'approved' => 'emerald',
            'rejected' => 'primary',
            default => 'cyan',
        };
    }
}
