<?php

/**
 * This file is part of the Infinite InfiniteApiSupportBundle project.
 *
 * (c) Infinite Networks Pty Ltd <http://www.infinite.net.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infinite\ApiSupportBundle\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class ApiEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([
            'error' => JsonResponse::HTTP_FORBIDDEN,
            'message' => $authException ? $authException->getMessage() : 'Access Denied',
        ], JsonResponse::HTTP_FORBIDDEN);
    }
}
