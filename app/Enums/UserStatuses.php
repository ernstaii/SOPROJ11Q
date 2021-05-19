<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserStatuses extends Enum
{
    const None = "";
    const Playing = "playing";
    const Caught = "caught";
    const InLobby = "in-lobby";
    const Retired = "retired";
    const Disconnected = "disconnected";
}
