<?php

namespace App\Models\Types;

enum OrderItemStatusEnum: string
{
    case WAITING = 'W';
    case PREPARING = 'P';
    case READY = 'R';

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
