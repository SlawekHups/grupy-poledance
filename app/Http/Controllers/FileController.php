<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Wyświetl miniaturę obrazka
     */
    public function thumbnail($path)
    {
        // Znajdź plik po ścieżce - sprawdź oba formaty
        $file = File::where('path', 'uploads/' . $path)->first();
        
        if (!$file) {
            // Spróbuj bez prefiksu uploads/
            $file = File::where('path', $path)->first();
        }
        
        if (!$file) {
            abort(404, 'Plik nie został znaleziony');
        }
        
        // Sprawdź czy to obrazek
        if (strpos($file->mime_type, 'image/') !== 0) {
            abort(404, 'Plik nie jest obrazkiem');
        }
        
        // Obsłuż różne formaty ścieżek
        $filePath = $file->path;
        if (strpos($filePath, 'uploads/') !== 0) {
            $filePath = 'uploads/' . $filePath;
        }
        
        $fullPath = Storage::disk('admin_files')->path($filePath);
        
        if (!file_exists($fullPath)) {
            abort(404, 'Plik nie istnieje na dysku');
        }
        
        // Zwróć obrazek jako response (nie download)
        return response()->file($fullPath);
    }

    /**
     * Pobierz publiczny plik
     */
    public function download($path)
    {
        // Znajdź plik po ścieżce - sprawdź oba formaty
        $file = File::where('path', 'uploads/' . $path)->first();
        
        if (!$file) {
            // Spróbuj bez prefiksu uploads/
            $file = File::where('path', $path)->first();
        }
        
        if (!$file) {
            abort(404, 'Plik nie został znaleziony');
        }
        
        // Sprawdź czy plik jest publiczny
        if (!$file->is_public) {
            abort(403, 'Plik nie jest publiczny');
        }
        
        // Obsłuż różne formaty ścieżek
        $filePath = $file->path;
        if (strpos($filePath, 'uploads/') !== 0) {
            $filePath = 'uploads/' . $filePath;
        }
        
        $fullPath = Storage::disk('admin_files')->path($filePath);
        
        if (!file_exists($fullPath)) {
            abort(404, 'Plik nie istnieje na dysku');
        }
        
        return response()->download($fullPath, $file->original_name);
    }
}
