<?php
// rename_fix.php - Jalankan sekali untuk memperbaiki file lama
$files = glob('../uploads/*');
foreach ($files as $file) {
    if (is_file($file) && !str_contains($file, '.')) {
        $mime = mime_content_type($file);
        $ext = match($mime) {
            'image/jpeg' => '.jpg',
            'image/png' => '.png',
            'image/webp' => '.webp',
            default => ''
        };
        if ($ext) rename($file, $file . $ext);
    }
}
echo "Done!";
?>