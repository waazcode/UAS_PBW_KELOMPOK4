<?php

namespace App\Support;

class DisplayText
{
    public static function format(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $text = str_replace('-', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
