<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Exception;

class TestYandexS3 extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:yandex-s3';

    /**
     * The console command description.
     */
    protected $description = 'Test Yandex S3 connection and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Yandex S3 connection...');
        
        // Test 1: Check configuration
        $this->info('=== Configuration Check ===');
        $config = config('filesystems.disks.yandex');
        
        $this->line('Driver: ' . $config['driver']);
        $this->line('Key: ' . substr($config['key'], 0, 8) . '...');
        $this->line('Secret: ' . substr($config['secret'], 0, 8) . '...');
        $this->line('Region: ' . $config['region']);
        $this->line('Bucket: ' . $config['bucket']);
        $this->line('Endpoint: ' . $config['endpoint']);
        $this->line('Use Path Style: ' . ($config['use_path_style_endpoint'] ? 'true' : 'false'));
        
        // Test 2: Basic disk connection
        $this->info("\n=== Disk Connection Test ===");
        try {
            $disk = Storage::disk('yandex');
            $this->info('✓ Disk instance created successfully');
        } catch (Exception $e) {
            $this->error('✗ Failed to create disk instance: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        // Test 3: List bucket contents (basic connectivity)
        $this->info("\n=== Bucket Connectivity Test ===");
        try {
            $files = $disk->files();
            $this->info('✓ Successfully connected to bucket');
            $this->line('Files in bucket: ' . count($files));
            
            if (count($files) > 0) {
                $this->line('Sample files:');
                foreach (array_slice($files, 0, 5) as $file) {
                    $this->line('  - ' . $file);
                }
            }
        } catch (Exception $e) {
            $this->error('✗ Failed to connect to bucket: ' . $e->getMessage());
            $this->line('Full error: ' . get_class($e) . ': ' . $e->getMessage());
            
            // Check common error types
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, 'InvalidAccessKeyId') || 
                str_contains($errorMessage, 'SignatureDoesNotMatch')) {
                $this->error('🔍 Authentication issue detected. Please verify your credentials.');
            } elseif (str_contains($errorMessage, 'NoSuchBucket')) {
                $this->error('🔍 Bucket not found. Please verify your bucket name.');
            } elseif (str_contains($errorMessage, 'EndpointConnectionError')) {
                $this->error('🔍 Endpoint connection error. Please verify your endpoint URL.');
            }
            
            return Command::FAILURE;
        }
        
        // Test 4: Write test
        $this->info("\n=== Write Test ===");
        $testFileName = 'test-' . time() . '.txt';
        $testContent = 'This is a test file created at ' . now();
        
        try {
            $result = $disk->put($testFileName, $testContent);
            if ($result) {
                $this->info('✓ Successfully wrote test file: ' . $testFileName);
            } else {
                $this->error('✗ Write returned false (no exception thrown)');
                return Command::FAILURE;
            }
        } catch (Exception $e) {
            $this->error('✗ Failed to write test file: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        // Test 5: Read test
        $this->info("\n=== Read Test ===");
        try {
            $content = $disk->get($testFileName);
            if ($content === $testContent) {
                $this->info('✓ Successfully read test file with correct content');
            } else {
                $this->error('✗ Content mismatch in read test');
            }
        } catch (Exception $e) {
            $this->error('✗ Failed to read test file: ' . $e->getMessage());
        }
        
        // Test 6: Delete test
        $this->info("\n=== Cleanup Test ===");
        try {
            $result = $disk->delete($testFileName);
            if ($result) {
                $this->info('✓ Successfully deleted test file');
            } else {
                $this->warn('! Delete returned false (file may not exist)');
            }
        } catch (Exception $e) {
            $this->error('✗ Failed to delete test file: ' . $e->getMessage());
        }
        
        $this->info("\n🎉 All tests completed successfully!");
        return Command::SUCCESS;
    }
}
