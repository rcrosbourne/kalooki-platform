<?php

use App\Models\Game;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

uses(TestCase::class);
it('does not allow a guest to create a game', function () {
  $this->post('/kalooki/create')
    ->assertRedirect('/login');
});
it('allows a logged in user to create a game', function () {
  $this->withoutExceptionHandling();
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

it('generates an invite link when a game is created', function () {
  $this->withoutExceptionHandling();
  $this->actingAs(User::factory()->create())
    ->post('/kalooki/create');
  $this->assertNotNull(Game::first()->invite_link);
});
it('ensures that link is single-use only', function () {
  $this->actingAs(User::factory()->create())
    ->post('/kalooki/create');
  $inviteLink = Game::first()->invite_link;
  $this->actingAs(User::factory()->create())
    ->get($inviteLink)
    ->assertRedirect('/kalooki/' . Game::first()->id);
  $this->actingAs(User::factory()->create())
    ->get($inviteLink)
    ->assertNotFound('Game is full');
});
it('does not allow the creator of the game to join twice with the invite link', function () {
  $creator = User::factory()->create();
  $this->actingAs($creator)
    ->post('/kalooki/create');
  $inviteLink = Game::first()->invite_link;
  $this->actingAs($creator)
    ->get($inviteLink)
    ->assertNotFound('You are already in the game');
});

it('does not allow a game to start when it does not have enough players', function () {
  $this->actingAs(User::factory()->create())
    ->post('/kalooki/create');
  //start game
  $this->actingAs(User::factory()->create())
    ->post('/kalooki/' . Game::first()->id . '/start')
    ->assertRedirect('/kalooki/' . Game::first()->id);
});
it('allows a game to start when it has enough players', function () {
  $creator = User::factory()->create();
  $player = User::factory()->create();
  $this->actingAs($creator)
    ->post('/kalooki/create');
  $this->actingAs($player)
    ->get(Game::first()->invite_link);
  $this->actingAs($creator)->post('/kalooki/' . Game::first()->id . '/start')->assertOk();
});
it('does not allow the non-creator to start the game', function () {
  $creator = User::factory()->create();
  $player = User::factory()->create();
  $this->actingAs($creator)
    ->post('/kalooki/create');
  $this->actingAs($player)
    ->get(Game::first()->invite_link);
  $this->actingAs($player)->post('/kalooki/' . Game::first()->id . '/start')->assertForbidden('You are not the creator of this game');
});
it('shows start game button for the creator of the game', function () {
  $creator = User::factory()->create();
  $player = User::factory()->create();
  $this->actingAs($creator)
    ->post('/kalooki/create');
  $this->actingAs($player)
    ->get(Game::first()->invite_link);
  $this->actingAs($creator)->get(
    '/kalooki/' . Game::first()->id
  )->assertInertia(fn (Assert $page) => $page->component('WaitingRoom')->has('isCreator')->where('isCreator', TRUE));
  $this->actingAs($player)->get(
    '/kalooki/' . Game::first()->id
  )->assertInertia(fn (Assert $page) => $page->component('WaitingRoom')->has('isCreator')->where('isCreator', FALSE));

});