<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use App\Models\LibraryItemFile;
use App\Models\VisitorStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\LibraryAccessController;
use Illuminate\Support\Facades\Session;

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
     * Record visitor statistics.
     */
    protected function recordVisit(string $page, $item = null)
    {
        $accessToken = null;
        $tokenStr = Session::get('library_access_token');
        
        if ($tokenStr) {
            $accessToken = \App\Models\AccessToken::findValidToken($tokenStr);
        }
        
        // Record stats based on user type
        if (Auth::check()) {
            VisitorStat::recordVisit([
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'page_visited' => $page,
                'access_method' => Auth::user()->is_admin ? 'admin' : 'user',
                'has_consent' => true, // Admins and users implicitly provide consent
            ]);
        } elseif ($accessToken) {
            VisitorStat::recordVisit([
                'name' => $accessToken->name,
                'email' => $accessToken->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'page_visited' => $page,
                'access_method' => 'token',
                'has_consent' => true, // Token holders have provided consent during signup
            ]);
        }
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

        // Prepare query (with published filter)
        $query = LibraryItem::query();
        if (!Auth::check() || !Auth::user()->is_admin) {
            $query->where('is_published', true);
        }

        // Handle search term
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            // Get all matching scope (published/admin)
            $allItems = $query->orderBy('created_at', 'desc')->get();
            // Filter in PHP using multibyte case-insensitive match
            $filtered = $allItems->filter(function ($item) use ($search) {
                return mb_stripos(mb_strtolower($item->title, 'UTF-8'), mb_strtolower($search, 'UTF-8')) !== false;
            });
            // Manual pagination
            $perPage = 12;
            $page = $request->input('page', 1);
            $items = new \Illuminate\Pagination\LengthAwarePaginator(
                $filtered->forPage($page, $perPage),
                $filtered->count(),
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        } else {
            // Default listing with pagination
            $items = $query->orderBy('created_at', 'desc')->paginate(12);
        }

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

        // Record the visit
        $this->recordVisit('library.index');

        return view('library.index', compact('items', 'categories', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     * Admin only method
     */
    public function create()
    {
        // No need to check admin here since it's done via middleware
        
        // Get all unique categories and tags for the form
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
            
        // Sort alphabetically
        sort($categories);
        sort($tags);
        
        return view('library.create', compact('categories', 'tags'));
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
            // Basic validation for the library item
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'categories' => 'required|string',
                'tags' => 'nullable|array',
                'is_published' => 'boolean',
                
                // File validation - support multiple files
                'files' => 'nullable|array',
                'files.*' => 'file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240',
                'file_names' => 'nullable|array',
                'file_names.*' => 'nullable|string|max:255',
                
                // Video validation - support multiple videos
                'videos' => 'nullable|array',
                'videos.*' => 'url|max:255',
                'video_names' => 'nullable|array',
                'video_names.*' => 'nullable|string|max:255',
            ];
            
            $validated = $request->validate($rules);
            
            Log::info('Validation passed', ['validated' => $validated]);

            // Create the library item first
            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'categories' => [$validated['categories']], // Wrap category in array since DB expects array
                'tags' => $validated['tags'] ?? [],
                'is_published' => $request->has('is_published'),
                'added_by' => Auth::id(),
            ];

            Log::info('Prepared data for database', ['data' => $data]);

            $item = LibraryItem::create($data);
            Log::info('Library item created', ['item_id' => $item->id, 'item' => $item]);

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $index => $file) {
                    try {
                        Log::info('Uploading file', [
                            'file' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                        ]);
                        
                        // Use Yandex S3 storage
                        $path = $file->store('documents', 'yandex');
                        
                        // Get the display name from form or use original filename
                        $displayName = $validated['file_names'][$index] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        
                        $item->files()->create([
                            'type' => 'document',
                            'name' => $displayName,
                            'file_path' => $path,
                            'original_filename' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'file_size' => $file->getSize(),
                            'sort_order' => $index,
                        ]);
                        
                        Log::info('File uploaded successfully to Yandex S3', ['path' => $path]);
                    } catch (\Exception $e) {
                        Log::error('Failed to upload file', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        
                        // Delete the item if file upload fails
                        $item->delete();
                        
                        return back()->withInput()->withErrors([
                            'files' => __('app.file_upload_failed') . ': ' . $e->getMessage()
                        ]);
                    }
                }
            }

            // Handle video URLs
            if (!empty($validated['videos'])) {
                foreach ($validated['videos'] as $index => $videoUrl) {
                    if (!empty($videoUrl)) {
                        $displayName = $validated['video_names'][$index] ?? 'Video ' . ($index + 1);
                        
                        $item->files()->create([
                            'type' => 'video',
                            'name' => $displayName,
                            'external_url' => $videoUrl,
                            'sort_order' => ($request->hasFile('files') ? count($request->file('files')) : 0) + $index,
                        ]);
                        
                        Log::info('Video URL added', ['url' => $videoUrl, 'name' => $displayName]);
                    }
                }
            }

            return redirect()->route('library.index')
                ->with('success', __('app.item_created_successfully'));
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
                'error' => __('app.item_save_error') . ': ' . $e->getMessage()
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

        // Record the visit
        $this->recordVisit('library.show.'.$id, $item);

        return view('library.show', compact('item'));
    }

    /**
     * Download a document from the library (legacy method for backward compatibility).
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

        // Get the first document file for backward compatibility
        $firstDocument = $item->documents()->first();
        
        if (!$firstDocument) {
            abort(404);
        }

        // Record the download
        $this->recordVisit('library.download.'.$id, $item);

        try {
            return Storage::disk('yandex')->download(
                $firstDocument->file_path,
                $firstDocument->name . '.' . pathinfo($firstDocument->file_path, PATHINFO_EXTENSION)
            );
        } catch (\Exception $e) {
            Log::error('Error downloading file', [
                'error' => $e->getMessage(),
                'item_id' => $id,
                'file_path' => $firstDocument->file_path
            ]);
            
            return back()->withErrors([
                'error' => __('app.file_download_failed') . ': ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download a specific file.
     */
    public function downloadFile(string $fileId)
    {
        // Check access for non-admin users
        if (!Auth::check() || !Auth::user()->is_admin) {
            if ($redirect = $this->checkLibraryAccess()) {
                return $redirect;
            }
        }

        $file = LibraryItemFile::findOrFail($fileId);
        $item = $file->libraryItem;

        // Non-admin users can only download files from published items
        if (!$item->is_published && (!Auth::check() || !Auth::user()->is_admin)) {
            abort(404);
        }

        // Only allow downloading document files
        if ($file->type !== 'document' || !$file->file_path) {
            abort(404);
        }

        // Record the download
        $this->recordVisit('library.file.download.'.$fileId, $item);

        try {
            return Storage::disk('yandex')->download(
                $file->file_path,
                $file->name . '.' . pathinfo($file->file_path, PATHINFO_EXTENSION)
            );
        } catch (\Exception $e) {
            Log::error('Error downloading file', [
                'error' => $e->getMessage(),
                'file_id' => $fileId,
                'file_path' => $file->file_path
            ]);
            
            return back()->withErrors([
                'error' => __('app.file_download_failed') . ': ' . $e->getMessage()
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
        
        // Get all unique categories and tags for the form
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
            
        // Sort alphabetically
        sort($categories);
        sort($tags);
        
        return view('library.edit', compact('item', 'categories', 'tags'));
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

            // Log all incoming request data for debugging
            Log::info('Update request data:', [
                'all_data' => $request->all(),
                'categories' => $request->input('categories'),
            ]);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'categories' => 'required|string',
                'tags' => 'nullable|array',
                'is_published' => 'boolean',
                
                // File validation - support multiple files
                'files' => 'nullable|array',
                'files.*' => 'file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240',
                'file_names' => 'nullable|array',
                'file_names.*' => 'nullable|string|max:255',
                
                // Video validation - support multiple videos
                'videos' => 'nullable|array',
                'videos.*' => 'url|max:255',
                'video_names' => 'nullable|array',
                'video_names.*' => 'nullable|string|max:255',
                
                // For managing existing files
                'delete_files' => 'nullable|array',
                'delete_files.*' => 'integer|exists:library_item_files,id',
            ]);

            // Log validated data
            Log::info('Validated data:', [
                'validated' => $validated,
                'categories' => $validated['categories']
            ]);

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'categories' => [$validated['categories']], // Wrap category in array since DB expects array
                'tags' => $validated['tags'] ?? [],
                'is_published' => $request->has('is_published'),
            ];

            Log::info('Data prepared for database:', [
                'data' => $data
            ]);

            $item->update($data);

            // Handle file deletions
            if (!empty($validated['delete_files'])) {
                $filesToDelete = $item->files()->whereIn('id', $validated['delete_files'])->get();
                foreach ($filesToDelete as $file) {
                    $file->delete(); // This will trigger the model's deleting event to remove from storage
                    Log::info('File deleted', ['file_id' => $file->id, 'file_path' => $file->file_path]);
                }
            }

            // Handle new file uploads
            if ($request->hasFile('files')) {
                $maxSortOrder = $item->files()->max('sort_order') ?? -1;
                
                foreach ($request->file('files') as $index => $file) {
                    try {
                        Log::info('Uploading new file', [
                            'file' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                        ]);
                        
                        // Use Yandex S3 storage
                        $path = $file->store('documents', 'yandex');
                        
                        // Get the display name from form or use original filename
                        $displayName = $validated['file_names'][$index] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        
                        $item->files()->create([
                            'type' => 'document',
                            'name' => $displayName,
                            'file_path' => $path,
                            'original_filename' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'file_size' => $file->getSize(),
                            'sort_order' => $maxSortOrder + 1 + $index,
                        ]);
                        
                        Log::info('File uploaded successfully', ['path' => $path]);
                    } catch (\Exception $e) {
                        Log::error('Failed to upload file during update', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        
                        return back()->withInput()->withErrors([
                            'files' => __('app.file_upload_failed') . ': ' . $e->getMessage()
                        ]);
                    }
                }
            }

            // Handle new video URLs
            if (!empty($validated['videos'])) {
                $maxSortOrder = $item->files()->max('sort_order') ?? -1;
                $fileOffset = $request->hasFile('files') ? count($request->file('files')) : 0;
                
                foreach ($validated['videos'] as $index => $videoUrl) {
                    if (!empty($videoUrl)) {
                        $displayName = $validated['video_names'][$index] ?? 'Video ' . ($index + 1);
                        
                        $item->files()->create([
                            'type' => 'video',
                            'name' => $displayName,
                            'external_url' => $videoUrl,
                            'sort_order' => $maxSortOrder + 1 + $fileOffset + $index,
                        ]);
                        
                        Log::info('Video URL added during update', ['url' => $videoUrl, 'name' => $displayName]);
                    }
                }
            }

            Log::info('Library item updated', [
                'item_id' => $item->id,
                'updated_categories' => $item->categories
            ]);

            return redirect()->route('library.index')
                ->with('success', __('app.item_updated_successfully'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error during update:', [
                'errors' => $e->errors()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating library item', [
                'error' => $e->getMessage(),
                'item_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->withErrors([
                'error' => __('app.item_update_error') . ': ' . $e->getMessage()
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
            
            Log::info('Attempting to delete library item', [
                'item_id' => $id,
                'item_type' => $item->type,
                'file_path' => $item->file_path,
                'title' => $item->title
            ]);
            
            // If it's a document, delete the file from storage
            if ($item->type === 'document' && $item->file_path) {
                try {
                    $deleted = Storage::disk('yandex')->delete($item->file_path);
                    
                    if ($deleted) {
                        Log::info('File deleted successfully from Yandex S3', [
                            'file_path' => $item->file_path,
                            'item_id' => $id
                        ]);
                    } else {
                        Log::warning('File deletion returned false (file may not exist)', [
                            'file_path' => $item->file_path,
                            'item_id' => $id
                        ]);
                    }
                } catch (\Exception $fileError) {
                    Log::error('Error deleting file from Yandex S3', [
                        'error' => $fileError->getMessage(),
                        'file_path' => $item->file_path,
                        'item_id' => $id
                    ]);
                    
                    // Don't stop the deletion process if file deletion fails
                    // The database record should still be deleted
                }
            }
            
            $item->delete();
            
            Log::info('Library item deleted successfully', [
                'item_id' => $id,
                'title' => $item->title
            ]);
            
            return redirect()->route('library.index')
                ->with('success', __('app.item_deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Error deleting library item', [
                'error' => $e->getMessage(),
                'item_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'error' => __('app.item_delete_error') . ': ' . $e->getMessage()
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

        // Record the visit
        $this->recordVisit('library.category.'.$category);

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

        // Record the visit
        $this->recordVisit('library.tag.'.$tag);

        return view('library.index', compact('items', 'categories', 'tags'))
            ->with('activeTag', $tag);
    }
}
