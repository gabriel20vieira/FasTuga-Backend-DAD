<?php

namespace App\Models\Types;

use App\Traits\ToArray;
use App\Traits\ToString;

enum OrderStatus: string
{
    use ToString, ToArray;

    case PREPARING = 'P';
    case READY = 'R';
    case DELIVERED = 'D';
    case CANCELED = 'C';
}
