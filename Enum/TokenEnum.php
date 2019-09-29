<?php

namespace Alapin\CalculatorBundle\Enum;

class TokenEnum
{
    const PLUS  = '+';
    const MINUS = '-';
    const MULTIPLY  = '*';
    const DIVIDE   = '/';

    const FLOAT_POINT   = '.';
    const PAREN_LEFT    = '(';
    const PAREN_RIGHT   = ')';

    const OPERATORS     = [self::PLUS, self::MINUS, self::MULTIPLY, self::DIVIDE];
    const PARENTHESES   = [self::PAREN_LEFT, self::PAREN_RIGHT];
}