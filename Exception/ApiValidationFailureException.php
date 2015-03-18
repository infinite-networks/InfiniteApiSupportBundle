<?php

/**
 * This file is part of the Infinite ApiSupportBundle project.
 *
 * (c) Infinite Networks Pty Ltd <http://www.infinite.net.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infinite\ApiSupportBundle\Exception;

use Infinite\ApiSupportBundle\Validation\Violation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ApiValidationFailureException extends \Exception
{
    private $input;
    private $violations;

    public function __construct($input, ConstraintViolationListInterface $violations)
    {
        $this->input = $input;
        $this->violations = $violations;
    }

    public static function createWithMessage($input, $message, $path = null)
    {
        return new static($input, new ConstraintViolationList(array(
            new Violation($message, $path)
        )));
    }

    /**
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolations()
    {
        return $this->violations;
    }
}
