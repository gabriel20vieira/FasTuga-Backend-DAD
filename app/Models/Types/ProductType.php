<?php

namespace App\Models\Types;

enum ProductType: string
{
    case HOT_DISH = 'hot dish';
    case COLD_DISH = 'cold dish';
    case DRINK = 'drink';
    case DESSERT = 'dessert';

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
