<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'target_role',
        'target_tool',
        'prompt_text',
        'instruction',
        'tags',
        'is_featured',
        'copy_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'copy_count' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
