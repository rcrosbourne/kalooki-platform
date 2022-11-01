<?php

namespace App\Providers;

use App\Events\PlayerDiscardCardFromHand;
use App\Events\PlayerLayDownCards;
use App\Events\PlayerRequestsCardFromDiscardPile;
use App\Events\PlayerRequestsCardFromStockPile;
use App\Models\Kalooki;
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
      PlayerDiscardCardFromHand::class         => [
        Kalooki::class . '@playerDiscardCardFromHand',
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
