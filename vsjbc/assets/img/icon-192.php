<?php
// Gera ícone PNG 192x192 para PWA usando GD
header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');

$size = 192;
$img  = imagecreatetruecolor($size, $size);

// Fundo arredondado azul escuro
$bg   = imagecolorallocate($img, 30, 42, 59);    // #1e2a3b
$blue = imagecolorallocate($img, 26, 86, 219);    // #1a56db
$white= imagecolorallocate($img, 255, 255, 255);

// Preencher fundo
imagefilledrectangle($img, 0, 0, $size, $size, $bg);

// Círculo central azul
imagefilledellipse($img, $size/2, $size/2, $size * 0.85, $size * 0.85, $blue);

// Texto "VS" centralizado
$font   = 5; // fonte built-in maior
$text   = 'VS';
$tw     = imagefontwidth($font) * strlen($text);
$th     = imagefontheight($font);
$x      = ($size - $tw) / 2;
$y      = ($size - $th) / 2 - 10;
imagestring($img, $font, (int)$x, (int)$y, $text, $white);

$text2 = 'JBC';
$tw2   = imagefontwidth($font) * strlen($text2);
$x2    = ($size - $tw2) / 2;
imagestring($img, $font, (int)$x2, (int)($y + $th + 4), $text2, $white);

imagepng($img);
imagedestroy($img);
