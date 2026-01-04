<?php

namespace App\Console\Commands;

use App\Models\VisitorStat;
use Illuminate\Console\Command;

class CleanupOldVisitorStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:cleanup 
                            {--days=90 : Number of days to keep stats}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old visitor statistics to prevent database bloat';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        
        $cutoffDate = now()->subDays($days);
        
        $this->info("Looking for visitor stats older than {$cutoffDate->toDateString()}...");
        
        // Count records to be deleted
        $count = VisitorStat::where('visited_at', '<', $cutoffDate)->count();
        
        if ($count === 0) {
            $this->info('No old visitor stats found to clean up.');
            return Command::SUCCESS;
        }
        
        $this->warn("Found {$count} visitor stat records older than {$days} days.");
        
        if ($dryRun) {
            $this->info('DRY RUN: No records were deleted.');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Records to delete', $count],
                    ['Cutoff date', $cutoffDate->toDateString()],
                    ['Days to keep', $days],
                ]
            );
            return Command::SUCCESS;
        }
        
        if (!$this->confirm('Do you want to proceed with deletion?', true)) {
            $this->info('Cleanup cancelled.');
            return Command::SUCCESS;
        }
        
        $this->info('Deleting old visitor stats...');
        $bar = $this->output->createProgressBar($count);
        
        // Delete in chunks to avoid memory issues
        $deleted = 0;
        VisitorStat::where('visited_at', '<', $cutoffDate)
            ->chunkById(1000, function ($stats) use (&$deleted, $bar) {
                foreach ($stats as $stat) {
                    $stat->delete();
                    $deleted++;
                    $bar->advance();
                }
            });
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("Successfully deleted {$deleted} old visitor stat records.");
        
        // Show remaining stats
        $remaining = VisitorStat::count();
        $this->info("Remaining visitor stats: {$remaining}");
        
        return Command::SUCCESS;
    }
}
