<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id',
        'created_by',
        'title',
        'description',
        'date',
        'status',
        'published_at',
    ];

    protected $casts = [
        'date' => 'date',
        'published_at' => 'datetime',
    ];

    protected $appends = ['avatar'];

    // Relacje
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getAvatarAttribute(): string
    {
        // Zwracamy URL do domyślnego avatara
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->title) . '&background=random';
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->startOfDay())
            ->orderBy('date', 'asc');
    }

    public function scopePast($query)
    {
        return $query->where('date', '<', now()->startOfDay())
            ->orderBy('date', 'desc');
    }

    // Metody pomocnicze
    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function unpublish(): void
    {
        $this->update([
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPast(): bool
    {
        return $this->date->startOfDay()->lt(now()->startOfDay());
    }

    public function isUpcoming(): bool
    {
        return $this->date->startOfDay()->gte(now()->startOfDay());
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($lesson) {
            if (!empty($lesson->attachments)) {
                foreach ($lesson->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment);
                }
            }
        });

        static::saving(function ($lesson) {
            if (is_array($lesson->attachments)) {
                $lesson->attachments = array_values(array_filter($lesson->attachments));
            }
        });
    }
} 