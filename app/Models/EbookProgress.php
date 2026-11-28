<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookProgress extends Model
{
    protected $table = 'ebook_progress';

    protected $fillable = ['user_id', 'library_item_id', 'checklist_state'];

    protected function casts(): array
    {
        return ['checklist_state' => 'array'];
    }
}
