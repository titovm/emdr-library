<?php

use App\Models\AccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('guests must obtain library access', function () {
    $this->get(route('library.index'))
        ->assertRedirect(route('library.access'));
});

test('accepting the access terms creates a token and grants access', function () {
    Queue::fake();

    $response = $this->post(route('library.process-access'), [
        'name' => 'Test Therapist',
        'email' => 'therapist@example.com',
        'consent' => '1',
        'nda_consent' => '1',
    ]);

    $token = AccessToken::sole();

    $response
        ->assertRedirect(route('library.index'))
        ->assertSessionHas('library_access_token', $token->token);

    expect($token->expires_at->isSameDay(now()->addDays(365)))->toBeTrue();
});

test('access requires both consent checkboxes', function () {
    $this->post(route('library.process-access'), [
        'name' => 'Test Therapist',
        'email' => 'therapist@example.com',
        'consent' => '1',
    ])->assertSessionHasErrors('nda_consent');

    $this->assertDatabaseCount('access_tokens', 0);
});

test('expired tokens do not grant library access', function () {
    $token = AccessToken::create([
        'name' => 'Test Therapist',
        'email' => 'therapist@example.com',
        'token' => str_repeat('a', 64),
        'expires_at' => now()->subMinute(),
    ]);

    expect(AccessToken::findValidToken($token->token))->toBeNull();

    $this->withSession(['library_access_token' => $token->token])
        ->get(route('library.index'))
        ->assertRedirect(route('library.access'));
});
