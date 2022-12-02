<?php

namespace App\Models\Types;

use App\Traits\ToArray;
use App\Traits\ToString;

enum PaymentType: string
{
    use ToArray, ToString;

    case VISA = 'visa';
    case PAYPAL = 'paypal';
    case MBWAY = 'mbway';
}
