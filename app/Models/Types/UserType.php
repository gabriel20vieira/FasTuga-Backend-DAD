<?php

namespace App\Models\Types;

enum UserType: string
{
    case CHEF = 'EC';
    case DELIVERY = 'ED';
    case MANAGER = 'EM';
    case CUSTOMER = 'C';

    public static function toRule()
    {
        $string = "";
        foreach (self::cases() as $case) {
            $string .= $string ? "," . $case->value : $case->value;
        }
        return $string;
    }

    public static function toString()
    {
        $string = "";
        foreach (self::cases() as $case) {
            $string .= $string ? ", " . ucfirst($case->value) : ucfirst($case->value);
        }
        return $string;
    }

    public static function toArray()
    {
        $strings = [];
        foreach (self::cases() as $case) {
            $strings[] = $case->value;
        }
        return $strings;
    }
}
