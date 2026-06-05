<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$source = '/home/u216676786/property_images';
$link   = '/home/u216676786/public_html/property_images';

echo '<pre>';
echo 'Source exists: ' . (file_exists($source) ? 'YES' : 'NO') . PHP_EOL;
echo 'Link exists:   ' . (file_exists($link)   ? 'YES' : 'NO') . PHP_EOL;
echo 'Is symlink:    ' . (is_link($link)        ? 'YES' : 'NO') . PHP_EOL;

if (is_link($link)) {
    echo PHP_EOL . 'Symlink already exists — done.';
} elseif (file_exists($link)) {
    echo PHP_EOL . 'Real folder already exists at that path.';
} else {
    $ok = @symlink($source, $link);
    echo 'symlink() result: ' . ($ok ? 'SUCCESS' : 'FAILED') . PHP_EOL;
    if (!$ok) {
        echo PHP_EOL . 'symlink() is disabled. Use Hostinger File Manager:';
        echo PHP_EOL . '  hPanel -> Files -> File Manager';
        echo PHP_EOL . '  Navigate to /home/u216676786/';
        echo PHP_EOL . '  Right-click property_images -> Move -> /home/u216676786/public_html/property_images';
    }
}
echo '</pre>';
