<?php

namespace App\Traits;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

trait ToString
{
    public static function toRule()
    {
        return self::toString(",", false);
    }

    public static function toString($delimiter = ', ', $upperCase = true)
    {
        $string = "";
        foreach (self::cases() as $case) {

            $append = $case->value;

            if ($upperCase) {
                $append = ucfirst($append);
            }

            $string .= $string ? $delimiter . $append : $append;
        }
        return $string;
    }
}
