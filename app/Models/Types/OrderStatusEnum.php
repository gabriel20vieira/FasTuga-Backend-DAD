<?php

namespace App\Models\Types;

enum OrderStatusEnum: string
{
    case PREPARING = 'P';
    case READY = 'R';
    case DELIVERED = 'D';
    case CANCELED = 'C';

    public static function toRule()
    {
        $string = "";
        foreach (self::cases() as $case) {
            $string .= $string ? "," . $case->value : $case->value;
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
