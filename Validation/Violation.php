<?php

/**
 * This file is part of the Infinite ApiSupportBundle project.
 *
 * (c) Infinite Networks Pty Ltd <http://www.infinite.net.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infinite\ApiSupportBundle\Validation;

use Symfony\Component\Validator\ConstraintViolation;

/**
 * Represents a basic violation
 */
class Violation extends ConstraintViolation
{
    public function __construct($message, $path = '')
    {
        parent::__construct($message, $message, array(), null, $path, null);
    }
}
