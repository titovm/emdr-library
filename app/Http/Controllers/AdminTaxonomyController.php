<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminTaxonomyController extends Controller
{
    /**
     * Constructor to handle route-level middleware
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of categories and tags.
     */
    public function index()
    {
        // Get all unique categories from library items
        $categories = LibraryItem::pluck('categories')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Get all unique tags from library items
        $tags = LibraryItem::pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Sort alphabetically
        sort($categories);
        sort($tags);

        return view('admin.taxonomy.index', compact('categories', 'tags'));
    }

    /**
     * Store a new category.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $categoryName = $validated['name'];

        // Get all existing categories
        $allCategories = LibraryItem::pluck('categories')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Check if category already exists (case-insensitive)
        if (in_array($categoryName, array_map('strtolower', $allCategories))) {
            return back()->withErrors(['name' => 'This category already exists.'])->withInput();
        }

        // Category doesn't exist, so we'll create it by adding it to a dummy item
        // This is a simple way to make it available in the category list
        // The category will be visible in dropdown lists but not attached to any real items
        try {
            // Create a dummy library item that will never be displayed
            // It will only exist to store the category
            LibraryItem::create([
                'title' => 'Category Placeholder: ' . $categoryName,
                'description' => 'This is a placeholder for the category: ' . $categoryName,
                'type' => 'document',
                'categories' => [$categoryName],
                'is_published' => false,
                'added_by' => auth()->id(),
            ]);

            return redirect()->route('admin.taxonomy.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating category', [
                'error' => $e->getMessage(),
                'category' => $categoryName
            ]);
            
            return back()->withErrors([
                'error' => 'An error occurred while creating the category: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Store a new tag.
     */
    public function storeTag(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tagName = $validated['name'];

        // Get all existing tags
        $allTags = LibraryItem::pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Check if tag already exists (case-insensitive)
        if (in_array($tagName, array_map('strtolower', $allTags))) {
            return back()->withErrors(['name' => 'This tag already exists.'])->withInput();
        }

        // Tag doesn't exist, so we'll create it by adding it to a dummy item
        try {
            // Create a dummy library item that will never be displayed
            // It will only exist to store the tag
            LibraryItem::create([
                'title' => 'Tag Placeholder: ' . $tagName,
                'description' => 'This is a placeholder for the tag: ' . $tagName,
                'type' => 'document',
                'tags' => [$tagName],
                'is_published' => false,
                'added_by' => auth()->id(),
            ]);

            return redirect()->route('admin.taxonomy.index')
                ->with('success', 'Tag created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating tag', [
                'error' => $e->getMessage(),
                'tag' => $tagName
            ]);
            
            return back()->withErrors([
                'error' => 'An error occurred while creating the tag: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Update a category name.
     */
    public function updateCategory(Request $request)
    {
        $validated = $request->validate([
            'old_name' => 'required|string|max:255',
            'new_name' => 'required|string|max:255',
        ]);

        $oldName = $validated['old_name'];
        $newName = $validated['new_name'];

        try {
            // Find all library items with this category
            $items = LibraryItem::whereJsonContains('categories', $oldName)->get();
            
            foreach ($items as $item) {
                $categories = $item->categories;
                
                // Replace the old category with the new one
                $index = array_search($oldName, $categories);
                if ($index !== false) {
                    $categories[$index] = $newName;
                    $item->categories = $categories;
                    $item->save();
                }
            }

            return redirect()->route('admin.taxonomy.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating category', [
                'error' => $e->getMessage(),
                'old_name' => $oldName,
                'new_name' => $newName
            ]);
            
            return back()->withErrors([
                'error' => 'An error occurred while updating the category: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update a tag name.
     */
    public function updateTag(Request $request)
    {
        $validated = $request->validate([
            'old_name' => 'required|string|max:255',
            'new_name' => 'required|string|max:255',
        ]);

        $oldName = $validated['old_name'];
        $newName = $validated['new_name'];

        try {
            // Find all library items with this tag
            $items = LibraryItem::whereJsonContains('tags', $oldName)->get();
            
            foreach ($items as $item) {
                $tags = $item->tags;
                
                // Replace the old tag with the new one
                $index = array_search($oldName, $tags);
                if ($index !== false) {
                    $tags[$index] = $newName;
                    $item->tags = $tags;
                    $item->save();
                }
            }

            return redirect()->route('admin.taxonomy.index')
                ->with('success', 'Tag updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating tag', [
                'error' => $e->getMessage(),
                'old_name' => $oldName,
                'new_name' => $newName
            ]);
            
            return back()->withErrors([
                'error' => 'An error occurred while updating the tag: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete a category.
     */
    public function destroyCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $categoryName = $validated['name'];

        try {
            // Find all library items with this category
            $items = LibraryItem::whereJsonContains('categories', $categoryName)->get();
            
            foreach ($items as $item) {
                $categories = $item->categories;
                
                // Remove the category
                $categories = array_filter($categories, function($category) use ($categoryName) {
                    return $category !== $categoryName;
                });
                
                $item->categories = array_values($categories); // Reset array keys
                $item->save();
            }

            return redirect()->route('admin.taxonomy.index')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting category', [
                'error' => $e->getMessage(),
                'category' => $categoryName
            ]);
            
            return back()->withErrors([
                'error' => 'An error occurred while deleting the category: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete a tag.
     */
    public function destroyTag(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tagName = $validated['name'];

        try {
            // Find all library items with this tag
            $items = LibraryItem::whereJsonContains('tags', $tagName)->get();
            
            foreach ($items as $item) {
                $tags = $item->tags;
                
                // Remove the tag
                $tags = array_filter($tags, function($tag) use ($tagName) {
                    return $tag !== $tagName;
                });
                
                $item->tags = array_values($tags); // Reset array keys
                $item->save();
            }

            return redirect()->route('admin.taxonomy.index')
                ->with('success', 'Tag deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting tag', [
                'error' => $e->getMessage(),
                'tag' => $tagName
            ]);
            
            return back()->withErrors([
                'error' => 'An error occurred while deleting the tag: ' . $e->getMessage()
            ]);
        }
    }
}