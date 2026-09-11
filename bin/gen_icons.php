<?php
// One-time: generates the PWA icons referenced by manifest.json (images/icon-192.png,
// images/icon-512.png). Re-run only if you want to change the icon design.
$sizes = [192, 512];
$bg = [0x16, 0x17, 0x1c];   // matches the app's dark background
$fg = [0x7e, 0xd9, 0x57];   // matches the app's green accent (btn-primary)

foreach ($sizes as $size) {
    $img = imagecreatetruecolor($size, $size);
    $bgColor = imagecolorallocate($img, $bg[0], $bg[1], $bg[2]);
    imagefill($img, 0, 0, $bgColor);

    // Green filled circle, echoing the header's "logo-dot" ● mark.
    $fgColor = imagecolorallocate($img, $fg[0], $fg[1], $fg[2]);
    $r = (int) ($size * 0.28);
    imagefilledellipse($img, (int) ($size / 2), (int) ($size / 2), $r * 2, $r * 2, $fgColor);

    imagepng($img, __DIR__ . '/../images/icon-' . $size . '.png');
    imagedestroy($img);
    echo "Wrote images/icon-$size.png\n";
}
