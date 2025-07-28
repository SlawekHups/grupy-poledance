<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lesson_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    // Relacje
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    // Metody pomocnicze
    public function getFullPath(): string
    {
        return storage_path('app/' . $this->file_path);
    }

    public function getPublicUrl(): string
    {
        return url('storage/' . $this->file_path);
    }

    public function getFormattedSize(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage(): bool
    {
        return str_starts_with($this->file_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->file_type === 'application/pdf';
    }
} 