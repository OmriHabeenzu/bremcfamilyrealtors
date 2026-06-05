<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$publicHtml = __DIR__;                          // actual public_html path on this server
$parent     = dirname($publicHtml);             // one level up (domains/bremcfamilyrealtors.net/)
$grandpa    = dirname($parent);                 // two levels up (/home/u216676786/)

// Where the images should live (inside public_html so browser can reach them)
$dest = $publicHtml . '/property_images';

// Possible locations the user may have put the folder
$candidates = [
    $grandpa . '/property_images',
    $parent  . '/property_images',
    $publicHtml . '/property_images',
];

echo '<pre>';
echo 'public_html is: ' . $publicHtml . PHP_EOL;
echo 'Destination:    ' . $dest . PHP_EOL . PHP_EOL;

foreach ($candidates as $c) {
    echo 'Checking ' . $c . ' ... ' . (file_exists($c) ? 'EXISTS' : 'not found') . PHP_EOL;
}

echo PHP_EOL;

// Already in the right place
if (file_exists($dest) && is_dir($dest)) {
    $count = count(glob($dest . '/*.{jpg,jpeg,png}', GLOB_BRACE));
    echo '✅ property_images folder exists inside public_html with ' . $count . ' images. Done!';
    echo '</pre>';
    exit;
}

// Find where the images are and move the folder
$found = null;
foreach ($candidates as $c) {
    if (file_exists($c) && is_dir($c) && $c !== $dest) {
        $found = $c;
        break;
    }
}

if ($found) {
    echo 'Found images at: ' . $found . PHP_EOL;
    if (rename($found, $dest)) {
        $count = count(glob($dest . '/*.{jpg,jpeg,png}', GLOB_BRACE));
        echo '✅ Moved successfully! ' . $count . ' images now in public_html/property_images/. DELETE this file.';
    } else {
        echo '❌ rename() failed.' . PHP_EOL . PHP_EOL;
        echo 'Use File Manager in hPanel:' . PHP_EOL;
        echo '  Move: ' . $found . PHP_EOL;
        echo '  To:   ' . $dest;
    }
} else {
    // Create the folder so uploads work going forward
    if (mkdir($dest, 0755, true)) {
        echo '✅ Created empty property_images/ inside public_html.' . PHP_EOL;
        echo 'Images folder was not found in any expected location.' . PHP_EOL . PHP_EOL;
        echo 'Use File Manager to move your property_images folder to:' . PHP_EOL;
        echo '  ' . $dest;
    } else {
        echo '❌ Could not create folder either. Check permissions.';
    }
}

echo '</pre>';
