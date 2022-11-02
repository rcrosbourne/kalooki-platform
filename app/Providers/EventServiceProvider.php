<?php

namespace App\Providers;

use App\Events\PlayerDiscardCardFromHand;
use App\Events\PlayerEndsTurnNotification;
use App\Events\PlayerLayDownCards;
use App\Events\PlayerRequestsCardFromDiscardPile;
use App\Events\PlayerRequestsCardFromStockPile;
use App\Events\PlayerTurnNotification;
use App\Models\Kalooki;
use App\Models\Player;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider {

  /**
   * The event to listener mappings for the application.
   *
   * @var array<class-string, array<int, class-string>>
   */
  protected $listen
    = [
      Registered::class                        => [
        SendEmailVerificationNotification::class,
      ],
      PlayerRequestsCardFromStockPile::class   => [
        Kalooki::class . '@playerRequestsCardFromStockPile',
      ],
      PlayerRequestsCardFromDiscardPile::class => [
        Kalooki::class . '@playerRequestsCardFromDiscardPile',
      ],
      PlayerLayDownCards::class                => [
        Kalooki::class . '@playerLayDownCards',
      ],
      PlayerTurnNotification::class                => [
        Player::class . '@onPlayerTurnNotification',
      ],
      PlayerDiscardCardFromHand::class         => [
        Kalooki::class . '@playerDiscardCardFromHand',
      ],
      PlayerEndsTurnNotification::class         => [
        Kalooki::class . '@playerEndsTurn',
      ],
    ];

  /**
   * Register any events for your application.
   *
   * @return void
   */
  public function boot() {
  }

  /**
   * Determine if events and listeners should be automatically discovered.
   *
   * @return bool
   */
  public function shouldDiscoverEvents() {
    return FALSE;
  }

}
