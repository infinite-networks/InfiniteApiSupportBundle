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

use Infinite\ApiSupportBundle\Exception\RateLimitException;
use Noxlogic\RateLimitBundle\Annotation\RateLimit;
use Noxlogic\RateLimitBundle\Events\GenerateKeyEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class RateLimitListener
{
    private $excludedIps;
    private $logger;

    /**
     * @param string[] $excludedIps
     * @param LoggerInterface $logger
     */
    public function __construct(array $excludedIps, LoggerInterface $logger)
    {
        $this->excludedIps = $excludedIps;
        $this->logger = $logger;
    }

    /**
     * Exempt some IP addresses from rate limiting entirely
     *
     * @param FilterControllerEvent $event
     */
    public function excludeSpecialIps(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if (in_array($request->getClientIp(), $this->excludedIps)) {
            // Add a "fake" annotation that doesn't match this request.
            // This will replace any existing annotations and bypass the path check.
            $request->attributes->set('_x-rate-limit', array(
                new RateLimit(array('methods' => ['nomatch'])),
            ));
        }
    }

    public function handleRateLimitException(GetResponseForExceptionEvent $event)
    {
        if ($event->getException() instanceof RateLimitException) {
            $event->setResponse(new Response('Rate limit exceeded', 429));

            // To avoid getting hundreds of duplicate log messages, only log them when they're freshly exceeded.
            /** @var \Noxlogic\RateLimitBundle\Service\RateLimitInfo $rateLimitInfo */
            $request = $event->getRequest();
            $rateLimitInfo = $request->attributes->get('rate_limit_info');

            if ($rateLimitInfo->getCalls() == $rateLimitInfo->getLimit() + 1) {
                $this->logger->warning('Rate limit exceeded', array(
                    'host' => $request->getHost(),
                    'ip' => $request->getClientIp(),
                    'method' => $request->getMethod(),
                    'url' => $request->getSchemeAndHttpHost() . $request->getRequestUri(),
                ));
            }
        }
    }

    /**
     * Apply rate limits separately to different IPs.
     *
     * @param GenerateKeyEvent $event
     */
    public function setRateLimitKey(GenerateKeyEvent $event)
    {
        $event->addToKey($event->getRequest()->getClientIp());
    }
}
