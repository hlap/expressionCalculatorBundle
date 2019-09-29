<?php

namespace Alapin\CalculatorBundle\Service;

use Exception;

class CalculatorService
{
    private $calculationProviderList = [];

    /**
     * @param string $key
     * @param CalculatorInterface $provider
     */
    public function addProvider(string $key, CalculatorInterface $provider): void
    {
        $this->calculationProviderList[$key] = $provider;
    }

    /**
     * @param $providerName
     * @return CalculatorInterface
     * @throws Exception
     */
    private function getProvider($providerName): CalculatorInterface
    {
        if (empty($this->calculationProviderList[$providerName])) {
            throw new Exception('Calculation provider %s not registered.', $providerName);
        }

        return $this->calculationProviderList[$providerName];
    }

    public function calculate(string $expression, string $providerName)
    {
        $provider = $this->getProvider($providerName);
        $expressionResult = $provider->calculate($expression);

        return $expressionResult;
    }
}