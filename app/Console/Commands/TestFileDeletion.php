<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\LibraryItem;

class TestFileDeletion extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:file-deletion';

    /**
     * The console command description.
     */
    protected $description = 'Test file deletion functionality with Yandex S3';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing file deletion functionality...');
        
        try {
            $disk = Storage::disk('yandex');
            
            // Create a test file
            $testFileName = 'test-deletion-' . time() . '.txt';
            $testContent = 'This file will be deleted during testing';
            
            $this->info('Creating test file: ' . $testFileName);
            $created = $disk->put($testFileName, $testContent);
            
            if (!$created) {
                $this->error('Failed to create test file');
                return Command::FAILURE;
            }
            
            $this->info('✓ Test file created successfully');
            
            // Verify file exists
            if (!$disk->exists($testFileName)) {
                $this->error('Test file was not found after creation');
                return Command::FAILURE;
            }
            
            $this->info('✓ Test file existence verified');
            
            // Test deletion
            $this->info('Deleting test file...');
            $deleted = $disk->delete($testFileName);
            
            if ($deleted) {
                $this->info('✓ File deletion returned true');
            } else {
                $this->warn('! File deletion returned false');
            }
            
            // Verify file is gone
            if (!$disk->exists($testFileName)) {
                $this->info('✓ Test file successfully deleted from storage');
            } else {
                $this->error('✗ Test file still exists after deletion');
                return Command::FAILURE;
            }
            
            // Test listing existing library items with files
            $this->info("\nChecking existing library items with files...");
            $items = LibraryItem::where('type', 'document')
                               ->whereNotNull('file_path')
                               ->where('file_path', '!=', '')
                               ->get();
            
            $this->line('Found ' . $items->count() . ' library items with files:');
            
            foreach ($items as $item) {
                $exists = $disk->exists($item->file_path);
                $status = $exists ? '✓ EXISTS' : '✗ MISSING';
                $this->line("  ID {$item->id}: {$item->title}");
                $this->line("    File: {$item->file_path} [{$status}]");
            }
            
            $this->info("\n🎉 File deletion test completed successfully!");
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Error during file deletion test: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
