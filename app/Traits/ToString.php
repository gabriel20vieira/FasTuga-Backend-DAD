<?php

namespace App\Traits;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

trait ToString
{
    public static function toRule()
    {
        return self::toString(",");
    }

    public static function toString($delimiter = ', ')
    {
        $string = "";
        foreach (self::cases() as $case) {
            $string .= $string ? $delimiter . ucfirst($case->value) : ucfirst($case->value);
        }
        return $string;
    }
}
