<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'author',
        'drive_file_id',
        'drive_path',
        'file_type',
        'description',
        'metadata',
        'is_system_book',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_system_book' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }
}
