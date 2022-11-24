<?php

namespace App\Models\Types;

use App\Traits\ToArray;
use App\Traits\ToString;

enum UserType: string
{
    use ToArray, ToString;

    case CHEF = 'EC';
    case DELIVERY = 'ED';
    case MANAGER = 'EM';
    case CUSTOMER = 'C';
}
