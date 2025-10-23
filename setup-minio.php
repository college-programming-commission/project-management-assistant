<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Setting up MinIO buckets...\n";

try {
    $disk = \Illuminate\Support\Facades\Storage::disk('s3');
    
    // Тест запису
    $disk->put('test.txt', 'MinIO is working! ' . date('Y-m-d H:i:s'));
    echo "✓ S3 bucket is accessible\n";

    // Livewire диск
    $livewireDisk = \Illuminate\Support\Facades\Storage::disk('livewire-s3');
    $livewireDisk->put('test.txt', 'Livewire MinIO is working! ' . date('Y-m-d H:i:s'));
    echo "✓ Livewire S3 disk is accessible\n";

    // Налаштувати публічний доступ для аватарок
    echo "Setting public access for avatars...\n";
    $disk->setVisibility('avatars', 'public');
    echo "✓ Public access configured\n";

    echo "✓ MinIO setup completed successfully!\n";
    exit(0);

} catch (\Exception $e) {
    echo "✗ MinIO setup failed: " . $e->getMessage() . "\n";
    exit(1);
}
