<?php

namespace App\Support;

use Illuminate\Support\Facades\Session;

final class MathCaptcha
{
    public const SESSION_KEY = 'math_captcha_answer';

    /** @return array{a:int,b:int,question:string} */
    public static function challenge(): array
    {
        $a = random_int(2, 9);
        $b = random_int(1, 9);
        Session::put(self::SESSION_KEY, $a + $b);

        return [
            'a' => $a,
            'b' => $b,
            'question' => $a.' + '.$b,
        ];
    }

    public static function check(?string $answer): bool
    {
        $expected = Session::pull(self::SESSION_KEY);
        if ($expected === null) {
            return false;
        }

        return is_numeric($answer) && (int) $answer === (int) $expected;
    }
}
