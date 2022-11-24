<?php

namespace App\Traits;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

trait ToArray
{
    public static function toArray()
    {
        $strings = [];
        foreach (self::cases() as $case) {
            $strings[] = $case->value;
        }
        return $strings;
    }
}
