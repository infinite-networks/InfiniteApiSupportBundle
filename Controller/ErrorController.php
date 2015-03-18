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

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandlerInterface;
use Infinite\CommonBundle\View\View;
use Infinite\ApiSupportBundle\Error\ValidationErrors;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ErrorController
{
    /**
     * @var \FOS\RestBundle\View\ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @param ViewHandlerInterface $viewHandler
     */
    public function __construct(ViewHandlerInterface $viewHandler)
    {
        $this->viewHandler = $viewHandler;
    }

    /**
     * A validation error has occurred with the request body. Return a 400
     * status code with the validation errors serialized.
     *
     * The RequestValidationFailureListener will change the route's controller
     * if validation errors occur while trying to parse the request body.
     *
     * @param Request $request
     * @return View
     */
    public function validationAction(Request $request)
    {
        $attributes = $request->attributes;

        return $this->getErrorResponse($attributes->get('input'), $attributes->get('validationErrors'));
    }

    /**
     * Returns a Response for the validation failures.
     *
     * @param mixed $input
     * @param ConstraintViolationListInterface $violations
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getErrorResponse($input, ConstraintViolationListInterface $violations)
    {
        $error = new ValidationErrors;
        $error->input = $input;
        $error->violations = $violations;

        return $this->viewHandler->handle(View::create($error, 400, array(), array('Default', 'view')));
    }
}
