<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\LibraryAccessController;

class LibraryItemController extends Controller
{
    /**
     * Constructor to handle route-level middleware
     */
    public function __construct()
    {
        // Constructor no longer needs to handle auth checks as they are in route middleware
    }

    /**
     * Check if user has access to the library.
     */
    protected function checkLibraryAccess()
    {
        // For regular users accessing the library
        if (!Auth::check()) {
            // If they're not authenticated, check if they have a valid access token
            if (!LibraryAccessController::hasAccess()) {
                return redirect()->route('library.access');
            }
        }
        
        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check access first for non-admin users
        if (!Auth::check() || !Auth::user()->is_admin) {
            if ($redirect = $this->checkLibraryAccess()) {
                return $redirect;
            }
        }

        // All users see published items
        $query = LibraryItem::query();
        
        // Admin users can see all items, others only see published
        if (!Auth::check() || !Auth::user()->is_admin) {
            $query->where('is_published', true);
        }
        
        $items = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get all unique categories and tags for the sidebar
        $categories = LibraryItem::pluck('categories')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $tags = LibraryItem::pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return view('library.index', compact('items', 'categories', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     * Admin only method
     */
    public function create()
    {
        // No need to check admin here since it's done via middleware
        return view('library.create');
    }

    /**
     * Store a newly created resource in storage.
     * Admin only method
     */
    public function store(Request $request)
    {
        // No need to check admin here since it's done via middleware
        
        // Log that we've entered the store method
        Log::info('Entered LibraryItemController@store method', [
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'request_all' => $request->all()
        ]);

        try {
            // Build validation rules - external_url only required for video type
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:document,video',
                'categories' => 'nullable|array',
                'tags' => 'nullable|array',
                'is_published' => 'boolean',
            ];
            
            // Add conditional rules based on type
            if ($request->type === 'document') {
                $rules['file'] = 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240';
                $rules['external_url'] = 'nullable'; // Not required for documents
            } else if ($request->type === 'video') {
                $rules['external_url'] = 'required|url|max:255';
                $rules['file'] = 'nullable'; // Not required for videos
            }
            
            $validated = $request->validate($rules);
            
            Log::info('Validation passed', ['validated' => $validated]);

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'categories' => $validated['categories'] ?? [],
                'tags' => $validated['tags'] ?? [],
                'is_published' => $request->has('is_published'),
                'added_by' => Auth::id(),
            ];

            Log::info('Prepared data for database', ['data' => $data]);

            // Handle file upload or external URL
            if ($validated['type'] === 'document' && $request->hasFile('file')) {
                try {
                    Log::info('Uploading file', [
                        'file' => $request->file('file')->getClientOriginalName(),
                        'file_size' => $request->file('file')->getSize(),
                        'mime_type' => $request->file('file')->getMimeType(),
                    ]);
                    
                    // Use the local disk instead of Yandex
                    $path = $request->file('file')->store('documents', 'local');
                    $data['file_path'] = $path;
                    Log::info('File uploaded successfully', ['path' => $path]);
                } catch (\Exception $e) {
                    Log::error('Failed to upload file', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    return back()->withInput()->withErrors([
                        'file' => 'Failed to upload file: ' . $e->getMessage()
                    ]);
                }
            } elseif ($validated['type'] === 'video') {
                $data['external_url'] = $validated['external_url'];
                Log::info('Video URL added', ['url' => $validated['external_url']]);
            }

            $item = LibraryItem::create($data);
            Log::info('Library item created', ['item_id' => $item->id, 'item' => $item]);

            return redirect()->route('library.index')
                ->with('success', 'Library item created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating library item', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->withErrors([
                'error' => 'An error occurred while saving the library item: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Check access for non-admin users
        if (!Auth::check() || !Auth::user()->is_admin) {
            if ($redirect = $this->checkLibraryAccess()) {
                return $redirect;
            }
        }

        $item = LibraryItem::findOrFail($id);

        // Non-admin users can only see published items
        if (!$item->is_published && (!Auth::check() || !Auth::user()->is_admin)) {
            abort(404);
        }

        return view('library.show', compact('item'));
    }

    /**
     * Download a document from the library.
     */
    public function download(string $id)
    {
        // Check access for non-admin users
        if (!Auth::check() || !Auth::user()->is_admin) {
            if ($redirect = $this->checkLibraryAccess()) {
                return $redirect;
            }
        }

        $item = LibraryItem::findOrFail($id);

        // Non-admin users can only download published items
        if (!$item->is_published && (!Auth::check() || !Auth::user()->is_admin)) {
            abort(404);
        }

        if ($item->type !== 'document' || !$item->file_path) {
            abort(404);
        }

        try {
            return Storage::disk('local')->download(
                $item->file_path,
                $item->title . '.' . pathinfo($item->file_path, PATHINFO_EXTENSION)
            );
        } catch (\Exception $e) {
            Log::error('Error downloading file', [
                'error' => $e->getMessage(),
                'item_id' => $id,
                'file_path' => $item->file_path
            ]);
            
            return back()->withErrors([
                'error' => 'Unable to download file: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Admin only method
     */
    public function edit(string $id)
    {
        // No need to check admin here since it's done via middleware
        $item = LibraryItem::findOrFail($id);
        
        return view('library.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     * Admin only method
     */
    public function update(Request $request, string $id)
    {
        // No need to check admin here since it's done via middleware
        try {
            $item = LibraryItem::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:document,video',
                'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240',
                'external_url' => 'required_if:type,video|url|max:255',
                'categories' => 'nullable|array',
                'tags' => 'nullable|array',
                'is_published' => 'boolean',
            ]);

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'categories' => $validated['categories'] ?? [],
                'tags' => $validated['tags'] ?? [],
                'is_published' => $request->has('is_published'),
            ];

            // Handle file upload or external URL
            if ($validated['type'] === 'document' && $request->hasFile('file')) {
                // Delete old file if exists
                if ($item->file_path) {
                    Storage::disk('local')->delete($item->file_path);
                }
                
                $path = $request->file('file')->store('documents', 'local');
                $data['file_path'] = $path;
                Log::info('File updated successfully', ['path' => $path]);
            } elseif ($validated['type'] === 'video') {
                $data['external_url'] = $validated['external_url'];
                
                // Clear file path if switching from document to video
                if ($item->type === 'document' && $item->file_path) {
                    Storage::disk('local')->delete($item->file_path);
                    $data['file_path'] = null;
                }
            }

            $item->update($data);
            Log::info('Library item updated', ['item_id' => $item->id]);

            return redirect()->route('library.index')
                ->with('success', 'Library item updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating library item', [
                'error' => $e->getMessage(),
                'item_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->withErrors([
                'error' => 'An error occurred while updating the library item: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Admin only method
     */
    public function destroy(string $id)
    {
        // No need to check admin here since it's done via middleware
        try {
            $item = LibraryItem::findOrFail($id);
            
            // If it's a document, delete the file from storage
            if ($item->type === 'document' && $item->file_path) {
                Storage::disk('local')->delete($item->file_path);
            }
            
            $item->delete();
            
            return redirect()->route('library.index')
                ->with('success', 'Library item deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting library item', [
                'error' => $e->getMessage(),
                'item_id' => $id
            ]);
            
            return back()->withErrors([
                'error' => 'An error occurred while deleting the library item: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Filter library items by category.
     */
    public function filterByCategory(string $category)
    {
        // Check access for non-admin users
        if (!Auth::check() || !Auth::user()->is_admin) {
            if ($redirect = $this->checkLibraryAccess()) {
                return $redirect;
            }
        }

        $query = LibraryItem::whereJsonContains('categories', $category);
        
        // Admin users can see all items, others only see published
        if (!Auth::check() || !Auth::user()->is_admin) {
            $query->where('is_published', true);
        }
        
        $items = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get all unique categories and tags for the sidebar
        $categories = LibraryItem::pluck('categories')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $tags = LibraryItem::pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return view('library.index', compact('items', 'categories', 'tags'))
            ->with('activeCategory', $category);
    }

    /**
     * Filter library items by tag.
     */
    public function filterByTag(string $tag)
    {
        // Check access for non-admin users
        if (!Auth::check() || !Auth::user()->is_admin) {
            if ($redirect = $this->checkLibraryAccess()) {
                return $redirect;
            }
        }

        $query = LibraryItem::whereJsonContains('tags', $tag);
        
        // Admin users can see all items, others only see published
        if (!Auth::check() || !Auth::user()->is_admin) {
            $query->where('is_published', true);
        }
        
        $items = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get all unique categories and tags for the sidebar
        $categories = LibraryItem::pluck('categories')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $tags = LibraryItem::pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return view('library.index', compact('items', 'categories', 'tags'))
            ->with('activeTag', $tag);
    }
}
