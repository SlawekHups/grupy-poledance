<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'created_by',
    ];

    // Relacje
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Metody pomocnicze
    public function createLesson(Group $group, string $date): Lesson
    {
        return Lesson::create([
            'group_id' => $group->id,
            'created_by' => $this->created_by,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $date,
            'status' => 'draft',
        ]);
    }
} 