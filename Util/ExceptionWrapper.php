<?php

/**
 * This file is part of the Infinite ApiSupportBundle project.
 *
 * (c) Infinite Networks Pty Ltd <http://www.infinite.net.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infinite\ApiSupportBundle\Util;

use FOS\RestBundle\Util\ExceptionWrapper as BaseExceptionWrapper;

class ExceptionWrapper extends BaseExceptionWrapper
{
    private $trace;

    public function __construct($data)
    {
        parent::__construct($data);

        if (isset($data['trace'])) {
            $this->trace = $data['trace'];
        }
    }
}
