<?php

namespace App\Models\Types;

use App\Traits\ToArray;
use App\Traits\ToString;

enum OrderItemStatus: string
{
    use ToString, ToArray;

    case WAITING = 'W';
    case PREPARING = 'P';
    case READY = 'R';
}
