<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CreateTestImage extends Command
{
    protected $signature = 'test:create-image {filename}';
    protected $description = 'Create a test image file for debugging';

    public function handle()
    {
        $filename = $this->argument('filename');
        $path = 'menu-items/' . $filename;
        
        // Create a simple test image content (base64 encoded small PNG)
        $testImageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        
        if (Storage::disk('public')->put($path, $testImageContent)) {
            $this->info("Test image created: {$path}");
            $this->info("Full path: " . storage_path('app/public/' . $path));
            $this->info("URL: " . Storage::url($path));
        } else {
            $this->error("Failed to create test image");
        }
    }
} 