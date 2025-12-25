<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestMaxFileSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:max-file-size';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and display PHP file upload size limits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== PHP File Upload Configuration ===');
        $this->newLine();

        // Get PHP configuration values
        $uploadMaxFilesize = ini_get('upload_max_filesize');
        $postMaxSize = ini_get('post_max_size');
        $maxExecutionTime = ini_get('max_execution_time');
        $maxInputTime = ini_get('max_input_time');
        $memoryLimit = ini_get('memory_limit');

        // Display configuration
        $this->table(
            ['Setting', 'Value', 'Bytes'],
            [
                ['upload_max_filesize', $uploadMaxFilesize, $this->convertToBytes($uploadMaxFilesize)],
                ['post_max_size', $postMaxSize, $this->convertToBytes($postMaxSize)],
                ['max_execution_time', $maxExecutionTime . ' seconds', 'N/A'],
                ['max_input_time', $maxInputTime . ' seconds', 'N/A'],
                ['memory_limit', $memoryLimit, $this->convertToBytes($memoryLimit)],
            ]
        );

        $this->newLine();

        // Determine effective upload limit
        $uploadBytes = $this->convertToBytes($uploadMaxFilesize);
        $postBytes = $this->convertToBytes($postMaxSize);
        
        $effectiveLimit = min($uploadBytes, $postBytes);
        $effectiveLimitMB = round($effectiveLimit / 1024 / 1024, 2);

        if ($uploadBytes < $postBytes) {
            $this->warn("⚠️  Effective upload limit: {$effectiveLimitMB}MB (limited by upload_max_filesize)");
        } else {
            $this->warn("⚠️  Effective upload limit: {$effectiveLimitMB}MB (limited by post_max_size)");
        }

        $this->newLine();

        // Laravel validation settings
        $this->info('=== Laravel File Validation Settings ===');
        $this->line('Check your controller validation rules for file size limits.');
        $this->line('Example: \'file|max:10240\' means 10MB max per file');

        $this->newLine();

        // Recommendations
        $this->info('=== Recommendations ===');
        
        if ($effectiveLimitMB < 50) {
            $this->error("❌ Upload limit ({$effectiveLimitMB}MB) is below 50MB");
            $this->line('');
            $this->line('To increase limits:');
            $this->line('');
            $this->line('For Laravel Herd (local):');
            $this->line('  1. Edit: ~/.config/herd/php.ini');
            $this->line('  2. Add: upload_max_filesize = 50M');
            $this->line('  3. Add: post_max_size = 50M');
            $this->line('  4. Run: herd restart');
            $this->line('');
            $this->line('For Production Server:');
            $this->line('  1. Edit: /etc/php/8.x/fpm/php.ini (adjust version)');
            $this->line('  2. Update: upload_max_filesize = 50M');
            $this->line('  3. Update: post_max_size = 50M');
            $this->line('  4. Restart: sudo systemctl restart php8.x-fpm');
            $this->line('  5. For Nginx, also set: client_max_body_size 50M;');
            $this->line('  6. Restart: sudo systemctl restart nginx');
        } else {
            $this->info("✅ Upload limit ({$effectiveLimitMB}MB) is adequate");
        }

        $this->newLine();

        // PHP info location
        $this->info('=== PHP Configuration File ===');
        $phpIni = php_ini_loaded_file();
        if ($phpIni) {
            $this->line("Loaded: {$phpIni}");
        } else {
            $this->warn('No php.ini file loaded');
        }

        $additionalInis = php_ini_scanned_files();
        if ($additionalInis) {
            $this->line('Additional .ini files:');
            foreach (explode(',', $additionalInis) as $ini) {
                $this->line('  - ' . trim($ini));
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Convert PHP size notation to bytes
     */
    private function convertToBytes($val)
    {
        if (empty($val)) {
            return 0;
        }

        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        $val = (int) $val;

        switch ($last) {
            case 'g':
                $val *= 1024;
                // Fall through
            case 'm':
                $val *= 1024;
                // Fall through
            case 'k':
                $val *= 1024;
        }

        return $val;
    }
}
