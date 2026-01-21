<?php

namespace App\Console\Commands;

use App\Models\LibraryItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixCategoryEncoding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:fix-encoding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Unicode encoding in categories and tags fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix category and tag encoding...');
        
        $items = LibraryItem::all();
        $count = 0;
        
        foreach ($items as $item) {
            try {
                // Get the raw JSON from database
                $categoriesRaw = $item->getAttributes()['categories'] ?? null;
                $tagsRaw = $item->getAttributes()['tags'] ?? null;
                
                $needsUpdate = false;
                
                // Check if categories need fixing (contains Unicode escapes like \u0414)
                if ($categoriesRaw && strpos($categoriesRaw, '\u') !== false) {
                    // Decode the JSON (which will convert \u0414 to actual Unicode)
                    $categories = json_decode($categoriesRaw, true);
                    // Re-encode with proper Unicode
                    $item->categories = $categories;
                    $needsUpdate = true;
                    $this->line("Fixed categories for item #{$item->id}: {$item->title}");
                }
                
                // Check if tags need fixing
                if ($tagsRaw && strpos($tagsRaw, '\u') !== false) {
                    $tags = json_decode($tagsRaw, true);
                    $item->tags = $tags;
                    $needsUpdate = true;
                    $this->line("Fixed tags for item #{$item->id}: {$item->title}");
                }
                
                if ($needsUpdate) {
                    $item->save();
                    $count++;
                }
                
            } catch (\Exception $e) {
                $this->error("Error fixing item #{$item->id}: {$e->getMessage()}");
                Log::error('Error fixing encoding', [
                    'item_id' => $item->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        $this->info("Completed! Fixed {$count} items.");
        
        return 0;
    }
}
