<?php

namespace Alapin\CalculatorBundle;

use Alapin\CalculatorBundle\DependencyInjection\Compile\CalculationProviderPass;
use Alapin\CalculatorBundle\Service\CalculatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CalculatorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->registerForAutoconfiguration(CalculatorInterface::class)->addTag(CalculatorInterface::TAG);
        $container->addCompilerPass(new CalculationProviderPass());
    }
}