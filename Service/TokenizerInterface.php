<?php

namespace Alapin\CalculatorBundle\Service;

interface TokenizerInterface
{
    public function tokenize(string $expression): array;
}