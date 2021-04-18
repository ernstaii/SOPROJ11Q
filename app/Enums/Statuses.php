<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Statuses extends Enum
{
    const None = "";
    const Config = "config";
    const Ongoing = "on-going";
    const Paused = "paused";
    const Finished = "finished";
}
