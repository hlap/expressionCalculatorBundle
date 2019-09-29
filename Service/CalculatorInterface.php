<?php

namespace Alapin\CalculatorBundle\Service;

interface CalculatorInterface
{
    public const TAG = 'alapin.expression_calculator.provider';

    public function calculate(string $expression): float;

    public function getName(): string;
}