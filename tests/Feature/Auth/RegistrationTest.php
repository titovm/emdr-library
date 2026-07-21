<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration is disabled', function () {
    $response = $this->get('/register');

    $response->assertNotFound();
});
