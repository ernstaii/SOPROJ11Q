<?php

namespace App\Events;

class ScoreUpdatedEvent extends GameEvent
{
    public $thiefScore;
    public $policeScore;

    public function __construct($gameId, $policeScore, $thiefScore)
    {
        parent::__construct($gameId);
        $this->policeScore = $policeScore;
        $this->thiefScore = $thiefScore;
    }

    public function broadcastAs()
    {
        return 'score.updated';
    }
}
