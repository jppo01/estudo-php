<?php
header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');

$size = 192;
$img  = imagecreatetruecolor($size, $size);
$bg   = imagecolorallocate($img, 30, 42, 59);
$blue = imagecolorallocate($img, 26, 86, 219);
$white= imagecolorallocate($img, 255, 255, 255);

imagefilledrectangle($img, 0, 0, $size, $size, $bg);
imagefilledellipse($img, $size/2, $size/2, $size * 0.85, $size * 0.85, $blue);

$font  = 5;
$text  = 'VS';
$tw    = imagefontwidth($font) * strlen($text);
$th    = imagefontheight($font);
$x     = ($size - $tw) / 2;
$y     = ($size - $th) / 2 - 10;
imagestring($img, $font, (int)$x, (int)$y, $text, $white);

$text2 = 'JBC';
$tw2   = imagefontwidth($font) * strlen($text2);
$x2    = ($size - $tw2) / 2;
imagestring($img, $font, (int)$x2, (int)($y + $th + 4), $text2, $white);

imagepng($img);
imagedestroy($img);
