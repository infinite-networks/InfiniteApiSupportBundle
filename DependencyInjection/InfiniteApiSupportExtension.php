<?php

/**
 * This file is part of the InfiniteApiSupportBundle project.
 *
 * (c) Infinite Networks Pty Ltd <http://www.infinite.net.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infinite\ApiSupportBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class InfiniteApiSupportExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('rate_limit.xml');
        $loader->load('request_validation.xml');
        $loader->load('services.xml');

        $container->getDefinition('infinite_api_support.listener.rate_limit')->addTag('monolog.logger', array(
            'channel' => $container->getParameter('infinite_api_support.listener.rate_limit.channel')
        ));
    }
}
