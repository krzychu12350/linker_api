<?php

namespace App\Enums;

use ArchTech\Enums\Values;

enum UserBanStatus: int
{
    use Values;

    case BANNED = 1;
    case UNBANNNED = 0;


}
