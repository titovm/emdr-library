<?php

use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admins can open taxonomy management', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get(route('admin.taxonomy.index'))
        ->assertOk()
        ->assertSee('Categories Management');
});

test('non admins cannot open taxonomy management', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('admin.taxonomy.index'))
        ->assertRedirect(route('home'));
});

test('admins can rename a category across library items', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $item = LibraryItem::create([
        'title' => 'Protocol guide',
        'categories' => ['Old category'],
        'tags' => [],
        'is_published' => true,
        'added_by' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->put(route('admin.taxonomy.categories.update'), [
            'old_name' => 'Old category',
            'new_name' => 'New category',
        ])
        ->assertRedirect(route('admin.taxonomy.index'));

    expect($item->refresh()->categories)->toBe(['New category']);
});
