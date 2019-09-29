<?php

namespace Alapin\CalculatorBundle\DependencyInjection\Compile;

use Alapin\CalculatorBundle\Service\CalculatorInterface;
use Alapin\CalculatorBundle\Service\CalculatorService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CalculationProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(CalculatorService::class)) {
            return;
        }

        $definition = $container->findDefinition(CalculatorService::class);
        $taggedServices = $container->findTaggedServiceIds(CalculatorInterface::TAG);

        foreach ($taggedServices as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addProvider', [$attributes['key'], new Reference($serviceId)]);
            }
        }
    }
}