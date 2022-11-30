<?php

namespace App\Models\Types;

use App\Traits\ToArray;
use App\Traits\ToString;

enum PaymentType: string
{
    use ToArray, ToString;

    case VISA = 'Visa';
    case PAYPAL = 'PayPal';
    case MBWAY = 'MbWay';
}
