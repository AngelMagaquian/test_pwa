<?php
// Crear imagen de 192x192
$image192 = imagecreatetruecolor(192, 192);
$bg = imagecolorallocate($image192, 0, 123, 255); // Color azul de Bootstrap
imagefill($image192, 0, 0, $bg);
imagepng($image192, 'icon-192x192.png');
imagedestroy($image192);

// Crear imagen de 512x512
$image512 = imagecreatetruecolor(512, 512);
$bg = imagecolorallocate($image512, 0, 123, 255); // Color azul de Bootstrap
imagefill($image512, 0, 0, $bg);
imagepng($image512, 'icon-512x512.png');
imagedestroy($image512);

echo "Iconos generados exitosamente.\n";
?> 