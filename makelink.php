<?php
// ONE-TIME SETUP SCRIPT — delete this file immediately after running it
$source = '/home/u216676786/property_images';
$link   = '/home/u216676786/public_html/property_images';

if (is_link($link)) {
    echo '✅ Symlink already exists and is working.';
} elseif (file_exists($link)) {
    echo '⚠️ A real folder called property_images already exists inside public_html. No symlink created.';
} elseif (symlink($source, $link)) {
    echo '✅ Symlink created! Images will now load. DELETE this file immediately.';
} else {
    echo '❌ Could not create symlink. Your host may not allow it. Use File Manager to MOVE the property_images folder into public_html instead.';
}
