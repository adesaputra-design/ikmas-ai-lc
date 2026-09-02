<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'event_date',
        'duration_minutes',
        'location_url',
        'speaker_name',
        'speaker_title',
        'status',
        'recording_url',
        'materials_url',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function getFormattedDateAttribute(): string
    {
        return $this->event_date->translatedFormat('l, d F Y — H:i') . ' WIB';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'upcoming' ? 'Upcoming' : 'Selesai';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status === 'upcoming' ? 'emerald' : 'secondary';
    }
}
