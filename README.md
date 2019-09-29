## Symfony Calculator Bundle for arithmetic expressions

### Basic usage

Typehint argument with `CalculatorService` if autowiring is enabled or pass it as argument of your service in configuration file. 

```php
use Alapin\CalculatorBundle\Service\CalculatorService;

public function showResult(CalculatorService $calculatorService)
{
    $expression = '(2.16 - 48.34)*2.7';
    $result = $calculatorService->calculate($expression, 'default')
    
    return $result;
}