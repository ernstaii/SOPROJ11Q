<?php

namespace App\Events;

class ScoreUpdatedEvent extends GameEvent
{
    public $thief_score;
    public $police_score;

    public function __construct($gameId, $policeScore, $thiefScore)
    {
        parent::__construct($gameId);
        $this->police_score = $policeScore;
        $this->thief_score = $thiefScore;
    }

    public function broadcastAs()
    {
        return 'score.updated';
    }
}
