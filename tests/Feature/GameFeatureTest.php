<?php

use App\Models\Game;
use App\Models\User;
use Tests\TestCase;

uses(TestCase::class);
it('does not allow a guest to create a game', function () {
  $this->post('/kalooki/create')
    ->assertRedirect('/login');
});
it('allows a logged in user to create a game', function () {
  $this->actingAs(User::factory()->create())
    ->post('/kalooki/create')
    ->assertRedirect('/kalooki/' . Game::first()->id);
});
it('creates a game with a unique code', function () {
  $this->actingAs(User::factory()->create())
    ->post('/kalooki/create');
  $this->actingAs(User::factory()->create())
    ->post('/kalooki/create');
  $this->assertCount(2, Game::all());
  $this->assertNotEquals(Game::first()->code, Game::skip(1)->first()->code);
});