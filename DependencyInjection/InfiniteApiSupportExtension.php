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
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('api_key.xml');
        $loader->load('rate_limit.xml');
        $loader->load('request_validation.xml');
        $loader->load('services.xml');

        $rateLimitDefinition = $container->getDefinition('infinite_api_support.listener.rate_limit');

        $rateLimitDefinition->replaceArgument(0, $config['rate_limit_excluded_ips']);

        $rateLimitDefinition->addTag('monolog.logger', array(
            'channel' => $container->getParameter('infinite_api_support.listener.rate_limit.channel')
        ));
    }
}
