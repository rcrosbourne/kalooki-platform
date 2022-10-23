<?php

use App\Models\Kalooki;
use App\Models\Player;

it('is unable to start unless there are at least 2 players', function () {
    $kalooki = new Kalooki();
    $kalooki->addPlayer(new Player('Player 1'));
    $kalooki->start();
    expect($kalooki->isStarted())->toBeFalse();
});