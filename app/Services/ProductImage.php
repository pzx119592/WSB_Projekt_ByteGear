<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ProductImage
{
    public function store(UploadedFile $file): string
    {
        // Ponowne zakodowanie usuwa EXIF i dodatkowe dane doklejone do pliku.
        $image = @imagecreatefromstring(file_get_contents($file->getRealPath()));
        if (! $image) {
            throw ValidationException::withMessages(['image' => 'Zdjęcie jest uszkodzone lub ma nieobsługiwany format.']);
        }
        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, 1600 / max($width, $height));
        $target = imagecreatetruecolor((int) round($width * $scale), (int) round($height * $scale));
        imagefill($target, 0, 0, imagecolorallocate($target, 255, 255, 255));
        imagecopyresampled($target, $image, 0, 0, 0, 0, imagesx($target), imagesy($target), $width, $height);
        ob_start();
        imagejpeg($target, null, 88);
        $bytes = ob_get_clean();
        imagedestroy($image);
        imagedestroy($target);
        $path = 'product-images/'.Str::uuid().'.jpg';
        if (! Storage::disk('local')->put($path, $bytes)) {
            throw new RuntimeException('Nie można zapisać zdjęcia.');
        }

        return $path;
    }
}
