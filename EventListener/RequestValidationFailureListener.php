<?php

/**
 * This file is part of the Infinite ApiSupportBundle project.
 *
 * (c) Infinite Networks Pty Ltd <http://www.infinite.net.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infinite\ApiSupportBundle\EventListener;

use Infinite\ApiSupportBundle\Controller\ErrorController;
use Infinite\ApiSupportBundle\Exception\ApiValidationFailureException;
use Infinite\CommonBundle\Activity\FailedActivityException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestValidationFailureListener
{
    private $errorHandlerController;

    /**
     * @param ErrorController $controller
     */
    public function __construct(ErrorController $controller)
    {
        $this->errorHandlerController = $controller;
    }

    public function checkRequestValidity(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $attributes = $request->attributes;
        /** @var ConstraintViolationListInterface $violations */
        $violations = $attributes->get('validationErrors');

        if (!$violations) {
            return;
        }

        if ($violations->count()) {
            $event->setController(array($this->errorHandlerController, 'validationAction'));
        }
    }

    public function handleValidationFailure(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        while (!$exception instanceof ApiValidationFailureException && $exception->getPrevious()) {
            $exception = $exception->getPrevious();
        }

        if (!$exception instanceof ApiValidationFailureException) {
            return;
        }

        $response = $this->errorHandlerController->getErrorResponse($exception->getInput(), $exception->getViolations());
        $event->setResponse($response);
    }
}
