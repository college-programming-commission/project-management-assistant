<?php
/**
 * MinIO Bucket Setup Script
 * 
 * Автоматично налаштовує MinIO при старті контейнера:
 * - Створює bucket (якщо не існує)
 * - Встановлює публічну bucket policy для всіх файлів
 * 
 * Запускається з entrypoint.sh, можна відключити через MINIO_AUTO_SETUP=false
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Setting up MinIO buckets...\n";

try {
    $disk = \Illuminate\Support\Facades\Storage::disk('s3');
    
    // Для setup використовуємо внутрішній endpoint
    $internalEndpoint = str_replace('https://s3-kafedra.phfk.college', 'http://minio:9000', config('filesystems.disks.s3.endpoint'));
    
    $s3Client = new \Aws\S3\S3Client([
        'version' => 'latest',
        'region' => config('filesystems.disks.s3.region'),
        'endpoint' => $internalEndpoint,
        'use_path_style_endpoint' => config('filesystems.disks.s3.use_path_style_endpoint'),
        'credentials' => [
            'key' => config('filesystems.disks.s3.key'),
            'secret' => config('filesystems.disks.s3.secret'),
        ],
    ]);
    
    $bucket = config('filesystems.disks.s3.bucket');
    
    // Перевірити чи існує bucket, якщо ні - створити
    if (!$s3Client->doesBucketExist($bucket)) {
        echo "Bucket {$bucket} does not exist, creating...\n";
        $s3Client->createBucket([
            'Bucket' => $bucket,
        ]);
        
        // Чекаємо трохи, щоб bucket створився
        sleep(2);
        echo "✓ Bucket {$bucket} created successfully\n";
    } else {
        echo "✓ Bucket {$bucket} already exists\n";
    }
    
    // Налаштувати публічну bucket policy
    echo "Setting public bucket policy...\n";
    
    $policy = [
        'Version' => '2012-10-17',
        'Statement' => [
            [
                'Effect' => 'Allow',
                'Principal' => ['AWS' => ['*']],
                'Action' => ['s3:GetObject'],
                'Resource' => ["arn:aws:s3:::{$bucket}/*"]
            ]
        ]
    ];
    
    $s3Client->putBucketPolicy([
        'Bucket' => $bucket,
        'Policy' => json_encode($policy)
    ]);
    
    echo "✓ Public bucket policy configured\n";
    
    // Тест запису
    $disk->put('test.txt', 'MinIO is working! ' . date('Y-m-d H:i:s'));
    echo "✓ S3 bucket is accessible\n";
    
    echo "✓ MinIO setup completed successfully!\n";
    echo "All files are now publicly accessible at: " . config('filesystems.disks.s3.url') . "\n";
    exit(0);

} catch (\Exception $e) {
    echo "✗ MinIO setup failed: " . $e->getMessage() . "\n";
    exit(1);
}
