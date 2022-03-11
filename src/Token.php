<?php

namespace App;

class Token
{
    public static function validate($token)
    {
        $tokens = file_get_contents(__DIR__ . '/../data/tokens.txt');
        $tokens = explode("\n", $tokens);
        $tokens = array_map('trim', $tokens);

        return in_array($token, $tokens);
    }

}
