<?php

/**
 * This file is part of the Infinite ApiSupportBundle project.
 *
 * (c) Infinite Networks Pty Ltd <http://www.infinite.net.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infinite\ApiSupportBundle;

use Noxlogic\RateLimitBundle\Service\RateLimitService as BaseRateLimitService;

// Override the base RateLimitService to expire rate limits reliably.

class RateLimitService extends BaseRateLimitService
{
    public function limitRate($key)
    {
        $limitInfo = parent::limitRate($key);

        if ($limitInfo && $limitInfo->getResetTimestamp() < time()) {
            parent::resetRate($key);
            $limitInfo = false;
        }

        return $limitInfo;
    }
}
