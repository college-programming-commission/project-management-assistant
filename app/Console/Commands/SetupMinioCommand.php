<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SetupMinioCommand extends Command
{
    protected $signature = 'minio:setup';
    protected $description = 'Setup MinIO buckets and permissions';

    public function handle()
    {
        $this->info('Setting up MinIO...');

        try {
            // Створити bucket якщо не існує
            $disk = Storage::disk('s3');
            
            if (!$disk->exists('.')) {
                $this->info('Creating bucket...');
                $disk->makeDirectory('.');
            }

            // Тест запису
            $disk->put('test.txt', 'MinIO is working!');
            $this->info('✓ Bucket is accessible');

            // Тест для livewire диска
            $livewireDisk = Storage::disk('livewire-s3');
            $livewireDisk->makeDirectory('livewire-tmp');
            $this->info('✓ Livewire disk is accessible');

            $this->info('✓ MinIO setup completed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('MinIO setup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
