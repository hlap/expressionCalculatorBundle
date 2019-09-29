<?php

namespace Alapin\CalculatorBundle\Service;

use Alapin\CalculatorBundle\Enum\TokenEnum;
use InvalidArgumentException;

class Tokenizer implements TokenizerInterface
{
    /**
     * @param string $expression
     *
     * @return array
     */
    public function tokenize(string $expression): array
    {
        if ($this->isExpressionNotValid($expression)) {
            throw new InvalidArgumentException('Invalid expression');
        }

        $tokens = [];
        $numberBuffer = '';
        $exprLength = strlen($expression);

        for ($i = 0; $i < $exprLength; $i++) {
            if ($expression[$i] === TokenEnum::MINUS && ($i === 0 || $expression[$i - 1] === TokenEnum::PAREN_LEFT)) {
                $numberBuffer .= $expression[$i];
            }

            if (ctype_digit($expression[$i]) || $expression[$i] === TokenEnum::FLOAT_POINT) {
                $numberBuffer .= $expression[$i];
            }

            if (!ctype_digit($expression[$i]) && $expression[$i] !== TokenEnum::FLOAT_POINT && strlen($numberBuffer) > 0) {
                if (!is_numeric($numberBuffer)) {
                    throw new InvalidArgumentException('Invalid float number');
                }

                $tokens[] = $numberBuffer;
                $numberBuffer = '';
                $i--;
            }

            if (in_array($expression[$i], TokenEnum::PARENTHESES)) {
                $previousToken = $tokens[count($tokens) - 1] ?? null;

                if ($tokens && $expression[$i] === TokenEnum::PAREN_LEFT &&
                    (is_numeric($previousToken) || in_array($previousToken, TokenEnum::PARENTHESES))) {
                    $tokens[] = TokenEnum::MULTIPLY;
                }

                $tokens[] = $expression[$i];
            }

            if (in_array($expression[$i], TokenEnum::OPERATORS)) {
                if ($i + 1 < $exprLength && in_array($expression[$i + 1], TokenEnum::OPERATORS)) {
                    throw new InvalidArgumentException('Invalid expression');
                }
                $tokens[] = $expression[$i];
            }
        }

        if(strlen($numberBuffer) > 0) {
            if(!is_numeric($numberBuffer)) {
                throw new InvalidArgumentException('Invalid float number');
            }

            $tokens[] = $numberBuffer;
        }

        return $tokens;
    }

    /**
     * @param string $expression
     *
     * @return false|int
     */
    private function isExpressionNotValid(string $expression)
    {
        return preg_match('/[^0-9.\+\-\/\*\(\)\s]+/', $expression);
    }
}