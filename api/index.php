<?php

// Buat direktori temporary wajib di Vercel jika belum ada
$tmpStorage = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/bootstrap/cache'
];

foreach ($tmpStorage as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Teruskan ke entry point Laravel public
require __DIR__ . '/../public/index.php';