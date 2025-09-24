<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'name',
        'original_name',
        'path',
        'mime_type',
        'size',
        'category',
        'description',
        'uploaded_by',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'size' => 'integer',
    ];

    /**
     * Relacja do użytkownika który przesłał plik
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Pobierz URL do pliku
     */
    public function getUrlAttribute(): string
    {
        // Obsłuż różne formaty ścieżek
        $path = $this->path;
        if (strpos($path, 'uploads/') !== 0) {
            $path = 'uploads/' . $path;
        }
        
        // Usuń 'uploads/' z początku ścieżki dla URL
        $urlPath = str_replace('uploads/', '', $path);
        
        // Zakoduj URL - spacje będą zakodowane jako %20
        $encodedUrlPath = rawurlencode($urlPath);
        
        // Użyj secure_url() aby wymusić HTTPS
        return secure_url('admin-files/' . $encodedUrlPath);
    }

    /**
     * Pobierz URL do miniaturki (działa dla wszystkich obrazków)
     */
    public function getThumbnailUrlAttribute(): string
    {
        // Obsłuż różne formaty ścieżek
        $path = $this->path;
        if (strpos($path, 'uploads/') !== 0) {
            $path = 'uploads/' . $path;
        }
        
        // Usuń 'uploads/' z początku ścieżki dla URL
        $urlPath = str_replace('uploads/', '', $path);
        
        // Zakoduj URL - spacje będą zakodowane jako %20
        $encodedUrlPath = rawurlencode($urlPath);
        
        // Użyj secure_url() aby wymusić HTTPS
        return secure_url('admin-files/thumbnails/' . $encodedUrlPath);
    }

    /**
     * Pobierz pełną ścieżkę do pliku
     */
    public function getFullPathAttribute(): string
    {
        return Storage::path($this->path);
    }

    /**
     * Sprawdź czy plik istnieje
     */
    public function exists(): bool
    {
        return Storage::exists($this->path);
    }

    /**
     * Pobierz rozmiar pliku w czytelnej formie
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Pobierz ikonę na podstawie typu MIME
     */
    public function getIconAttribute(): string
    {
        $mime = $this->mime_type;
        
        if (strpos($mime, 'image/') === 0) {
            return 'heroicon-o-photo';
        } elseif (strpos($mime, 'video/') === 0) {
            return 'heroicon-o-video-camera';
        } elseif (strpos($mime, 'audio/') === 0) {
            return 'heroicon-o-musical-note';
        } elseif (str_contains($mime, 'pdf')) {
            return 'heroicon-o-document-text';
        } elseif (str_contains($mime, 'word') || str_contains($mime, 'document')) {
            return 'heroicon-o-document';
        } elseif (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet')) {
            return 'heroicon-o-table-cells';
        } elseif (str_contains($mime, 'powerpoint') || str_contains($mime, 'presentation')) {
            return 'heroicon-o-presentation-chart-line';
        } elseif (str_contains($mime, 'zip') || str_contains($mime, 'rar') || str_contains($mime, 'archive')) {
            return 'heroicon-o-archive-box';
        } elseif (str_contains($mime, 'shell') || str_contains($mime, 'bash') || str_contains($mime, 'sh')) {
            return 'heroicon-o-code-bracket';
        } elseif (str_contains($mime, 'text') || str_contains($mime, 'plain')) {
            return 'heroicon-o-document-text';
        } elseif (str_contains($mime, 'csv') || str_contains($mime, 'comma-separated')) {
            return 'heroicon-o-table-cells';
        } elseif (str_contains($mime, 'json')) {
            return 'heroicon-o-code-bracket';
        } elseif (str_contains($mime, 'xml')) {
            return 'heroicon-o-code-bracket';
        } elseif (str_contains($mime, 'sql')) {
            return 'heroicon-o-code-bracket';
        } elseif (str_contains($mime, 'markdown') || str_contains($mime, 'md')) {
            return 'heroicon-o-document-text';
        } else {
            return 'heroicon-o-document';
        }
    }
}
