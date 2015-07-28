<?php

namespace djekl\Currency;

class Str
{
    public static function find($haystack, $start, $finish)
    {
        $s = explode($start, $haystack);

        if (empty($s[1])) {
            return false;
        }

        $s = explode($finish, $s[1]);

        return $s[0];
    }
}
