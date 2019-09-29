<?php

namespace Alapin\CalculatorBundle\Service\Provider;

use Alapin\CalculatorBundle\Enum\TokenEnum;
use Alapin\CalculatorBundle\Service\CalculatorInterface;
use Alapin\CalculatorBundle\Service\Tokenizer;
use Alapin\CalculatorBundle\Service\TokenizerInterface;
use InvalidArgumentException;
use SplStack;

class DefaultCalculator implements CalculatorInterface
{
    private $tokenizer;
    /**
     * Calculator constructor.
     * @param Tokenizer $tokenizer
     */
    public function __construct(TokenizerInterface $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    public function getName(): string
    {
        return 'default';
    }

    /**
     * @param string $expression
     * @return float|int
     */
    public function calculate(string $expression): float
    {
        $tokens = $this->tokenizer->tokenize($expression);
        $postfix = $this->getPostfixNotation($tokens);
        $result = $this->calculateFromPostfixNotation($postfix);

        return $result;
    }

    /**
     * @param array $tokens
     *
     * @return array
     */
    private function getPostfixNotation(array $tokens): array
    {
        $queue = [];
        $stack = new SplStack();
        $tokensCount = count($tokens);

        for ($i = 0; $i < $tokensCount; $i++) {
            if (is_numeric($tokens[$i])) {
                array_unshift($queue, (float)$tokens[$i]);
            }

            if (in_array($tokens[$i], TokenEnum::OPERATORS)) {
                $tokenOperatorPrecedence = $this->getOperatorPrecedence($tokens[$i]);

                while ($stack->count() > 0 && in_array($stack->top(), TokenEnum::OPERATORS)
                    && ($tokenOperatorPrecedence === $this->getOperatorPrecedence($stack->top())
                        || $tokenOperatorPrecedence < $this->getOperatorPrecedence($stack->top()))) {
                    array_unshift($queue, $stack->pop());
                }

                $stack->push($tokens[$i]);
            }

            if ($tokens[$i] === TokenEnum::PAREN_LEFT) {
                $stack->push(TokenEnum::PAREN_LEFT);
            }

            if ($tokens[$i] === TokenEnum::PAREN_RIGHT) {
                if (substr_count($stack->serialize(), TokenEnum::PAREN_LEFT) === 0) {
                    throw new InvalidArgumentException('Parenthesis are misplaced');
                }

                while ($stack->top() != TokenEnum::PAREN_LEFT) {
                    array_unshift($queue, $stack->pop());
                }

                $stack->pop();
            }
        }

        while ($stack->count() > 0) {
            array_unshift($queue, $stack->pop());
        }

        return $queue;
    }

    /**
     * @param array $queue
     * @return int|float
     */
    private function calculateFromPostfixNotation(array $queue)
    {
        $stack = new SplStack();

        while (count($queue) > 0) {
            $currentToken = array_pop($queue);

            if (is_numeric($currentToken)) {
                $stack->push($currentToken);
            }

            if (in_array($currentToken, TokenEnum::OPERATORS)) {
                if ($stack->count() < 2) {
                    throw new InvalidArgumentException('Invalid expression');
                }

                $stack->push($this->executeOperator($currentToken, $stack->pop(), $stack->pop()));
            }

        }

        if ($stack->count() === 1) {
            return $stack->pop();
        }

        throw new InvalidArgumentException('Invalid expression');
    }

    /**
     * @param $operator
     * @return int
     */
    private function getOperatorPrecedence($operator)
    {
        if (!in_array($operator, TokenEnum::OPERATORS)) {
            throw new InvalidArgumentException("Cannot check precedence of $operator operator");
        }

        if ($operator === TokenEnum::MULTIPLY || $operator === TokenEnum::DIVIDE) {
            return 4;
        }

        return 1;
    }

    /**
     * @param $operator
     * @param $a
     * @param $b
     * @return float|int
     */
    private function executeOperator($operator, $a, $b)
    {
        switch ($operator) {
            case TokenEnum::PLUS:
                $result = $a + $b;
                break;
            case TokenEnum::MINUS:
                $result = $b - $a;
                break;
            case TokenEnum::MULTIPLY:
                $result = $a * $b;
                break;
            case TokenEnum::DIVIDE:
                if ($a === 0) {
                    throw new InvalidArgumentException('Division by zero occured');
                }
                $result = $b / $a;
                break;
            default:
                throw new InvalidArgumentException('Unknown operator provided');
        }

        return $result;
    }
}
