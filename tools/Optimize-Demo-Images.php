<?php

// Zmiana formatu własnych zasobów na WebP; oryginały PNG pozostają do edycji.
$folder = dirname(__DIR__).'/public/images/products';
foreach (glob($folder.'/*.png') as $source) {
    $image = imagecreatefrompng($source);
    if (! $image || ! imagewebp($image, substr($source, 0, -4).'.webp', 88)) {
        throw new RuntimeException('Nie można zoptymalizować '.$source);
    }
    imagedestroy($image);
}
echo "Zapisano wersje WebP. Oryginaly PNG zachowano.\n";
