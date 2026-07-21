<?php

use App\Models\AccessToken;
use App\Models\LibraryItem;
use App\Models\LibraryItemFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
    Storage::fake('yandex');
});

function libraryAccessSession(): array
{
    $token = AccessToken::generate('Test Therapist', 'therapist@example.com');

    return ['library_access_token' => $token->token];
}

function createLibraryItem(User $owner, array $attributes = []): LibraryItem
{
    return LibraryItem::create(array_merge([
        'title' => 'Published resource',
        'description' => 'A resource for therapists.',
        'categories' => ['Protocols'],
        'tags' => ['Adults'],
        'is_published' => true,
        'added_by' => $owner->id,
    ], $attributes));
}

test('token visitors only see published items', function () {
    $owner = User::factory()->create(['is_admin' => true]);
    createLibraryItem($owner);
    $draft = createLibraryItem($owner, [
        'title' => 'Private draft',
        'is_published' => false,
    ]);

    $this->withSession(libraryAccessSession())
        ->get(route('library.index'))
        ->assertOk()
        ->assertSee('Published resource')
        ->assertDontSee('Private draft');

    $this->withSession(libraryAccessSession())
        ->get(route('library.show', $draft))
        ->assertNotFound();
});

test('admins can see unpublished items', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    createLibraryItem($admin, [
        'title' => 'Private draft',
        'is_published' => false,
    ]);

    $this->actingAs($admin)
        ->get(route('library.index'))
        ->assertOk()
        ->assertSee('Private draft');
});

test('admins can upload a document and its metadata', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $document = UploadedFile::fake()->create('protocol.pdf', 100, 'application/pdf');

    $this->actingAs($admin)
        ->post(route('library.store'), [
            'title' => 'Protocol guide',
            'description' => 'Guide description',
            'categories' => 'Protocols',
            'tags' => ['Adults'],
            'is_published' => '1',
            'files' => [$document],
            'file_names' => ['Protocol PDF'],
        ])
        ->assertRedirect(route('library.index'));

    $item = LibraryItem::where('title', 'Protocol guide')->sole();
    $file = $item->files()->sole();

    expect($file->name)->toBe('Protocol PDF')
        ->and($file->original_filename)->toBe('protocol.pdf')
        ->and($file->type)->toBe('document');
    Storage::disk('yandex')->assertExists($file->file_path);
});

test('authorized visitors can download documents', function () {
    $owner = User::factory()->create(['is_admin' => true]);
    $item = createLibraryItem($owner);
    Storage::disk('yandex')->put('documents/protocol.pdf', 'document contents');
    $file = $item->files()->create([
        'type' => 'document',
        'name' => 'Protocol',
        'file_path' => 'documents/protocol.pdf',
        'original_filename' => 'protocol.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 17,
    ]);

    $this->withSession(libraryAccessSession())
        ->get(route('library.file.download', $file))
        ->assertOk()
        ->assertDownload('Protocol.pdf');
});

test('deleting an item removes every document from object storage', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $item = createLibraryItem($admin);

    foreach (['first.pdf', 'second.docx'] as $index => $filename) {
        $path = 'documents/'.$filename;
        Storage::disk('yandex')->put($path, 'contents');
        $item->files()->create([
            'type' => 'document',
            'name' => pathinfo($filename, PATHINFO_FILENAME),
            'file_path' => $path,
            'original_filename' => $filename,
            'sort_order' => $index,
        ]);
    }

    $video = $item->files()->create([
        'type' => 'video',
        'name' => 'External video',
        'external_url' => 'https://example.com/video',
        'sort_order' => 2,
    ]);

    $this->actingAs($admin)
        ->delete(route('library.destroy', $item))
        ->assertRedirect(route('library.index'));

    Storage::disk('yandex')->assertMissing('documents/first.pdf');
    Storage::disk('yandex')->assertMissing('documents/second.docx');
    $this->assertDatabaseMissing('library_items', ['id' => $item->id]);
    $this->assertDatabaseMissing('library_item_files', ['id' => $video->id]);
    expect(LibraryItemFile::count())->toBe(0);
});
