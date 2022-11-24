<?php

namespace App\Models\Types;

use App\Traits\ToArray;
use App\Traits\ToString;

enum ProductType: string
{
    use ToArray, ToString;

    case HOT_DISH = 'hot dish';
    case COLD_DISH = 'cold dish';
    case DRINK = 'drink';
    case DESSERT = 'dessert';
}
