<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property mixed|string                $code
 * @property \App\Enums\GameStatus|mixed $status
 * @property mixed                       $created_by
 * @property mixed                       $players
 * @property mixed|string                $invite_link
 */
class Game extends Model {

  use HasFactory;

  protected $casts = [
      'status'  => 'App\Enums\GameStatus',
      'players' => 'array',
  ];

  public static function generateCode(): string {
    $code = Str::random(6);
    if (Game::where('code', $code)->exists()) {
      return Game::generateCode();
    }
    return $code;
  }

  public function creator(): BelongsTo {
    return $this->belongsTo(User::class, 'created_by');
  }
}
