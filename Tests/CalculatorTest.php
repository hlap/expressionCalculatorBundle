<?php

namespace Alapin\Test\Calculator;

use Alapin\CalculatorBundle\Service\CalculatorService;
use Alapin\CalculatorBundle\Service\Provider\DefaultCalculator;
use Alapin\CalculatorBundle\Service\Tokenizer;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    /** @var CalculatorService */
    protected $calculatorService;

    private $calculationProvider = 'default';

    public function setUp()
    {
        $this->calculatorService = new CalculatorService();
        $this->calculatorService->addProvider($this->calculationProvider, new DefaultCalculator(new Tokenizer()));
    }

    /**
     * @expectedException           \InvalidArgumentException
     */
    public function testMisplacedParenthesis()
    {
        $this->calculatorService->calculate(')5(', $this->calculationProvider);
    }

    /**
     * @expectedException           \InvalidArgumentException
     * @expectedExceptionMessage    Invalid expression
     */
    public function testDivisionByZero()
    {
        $this->calculatorService->calculate('1/0', $this->calculationProvider);
    }

    public function testCalculate()
    {
        $this->executeCalculation('250*14.3', 3575);
        $this->executeCalculation('3*6 / 117', 0.1538461538);
        $this->executeCalculation('(2.16 - 48.34)*2.3', -106.214);
        $this->executeCalculation('(59 - 15 + 3*6)/21', 2.952380952381);
        $this->executeCalculation('3-(4-6)', 5);
    }

    private function executeCalculation($expression, $expect)
    {
        $result = $this->calculatorService->calculate($expression, $this->calculationProvider);
        $this->assertEquals($expect, $result, $expression);
    }
}
