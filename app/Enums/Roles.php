<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class Roles extends Enum
{
    const None = "0";
    const Thief = "1";
    const UndercoverThief = "2";
    const Police = "3";
}
