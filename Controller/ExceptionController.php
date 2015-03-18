<?php

/**
 * This file is part of the Infinite ApiSupportBundle project.
 *
 * (c) Infinite Networks Pty Ltd <http://www.infinite.net.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infinite\ApiSupportBundle\Controller;

use FOS\RestBundle\Controller\ExceptionController as BaseExceptionController;
use FOS\RestBundle\View\ViewHandler;
use Infinite\ApiSupportBundle\Util\ExceptionWrapper;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController extends BaseExceptionController
{
    protected function createExceptionWrapper(array $parameters)
    {
        return new ExceptionWrapper($parameters);
    }

    protected function getParameters(
        ViewHandler $viewHandler,
        $currentContent,
        $code,
        $exception,
        DebugLoggerInterface $logger = null,
        $format = 'html'
    ) {
        $parameters = parent::getParameters($viewHandler, $currentContent, $code, $exception, $logger, $format);

        if ($this->container->get('kernel')->isDebug()) {
            $parameters['trace'] = $exception->getTrace();
        }

        return $parameters;
    }
}
