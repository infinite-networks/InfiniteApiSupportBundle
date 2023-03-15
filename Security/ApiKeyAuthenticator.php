<?php

/**
 * This file is part of the InfiniteApiSupportBundle project.
 *
 * (c) Infinite Networks Pty Ltd <http://www.infinite.net.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infinite\ApiSupportBundle\Security;

use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var string
     */
    private $realm;

    private $userProvider;

    /**
     * @param EntityUserProvider $userProvider
     * @param string $realm
     */
    public function __construct(EntityUserProvider $userProvider, $realm)
    {
        $this->userProvider = $userProvider;
        $this->realm = $realm;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $apiKey = $token->getCredentials();
        $user = $this->userProvider->loadUserByUsername($apiKey);

        if (!$user) {
            throw new AuthenticationException(sprintf('API Key "%s" does not exist.', $apiKey));
        }

        $roles = $user->getRoles();
        $roles[] = 'ROLE_API';

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            $roles
        );
    }

    public function createToken(Request $request, $providerKey)
    {
        if (!$request->headers->has('Authorization')) {
            return null;
        }

        $regex = sprintf('/%s (.*)/', $this->realm);
        if (1 !== preg_match($regex, $request->headers->get('Authorization'), $matches)) {
            return null;
        }

        if (!$matches[1]) {
            throw new BadCredentialsException;
        }

        return new PreAuthenticatedToken('anon.', $matches[1], $providerKey);
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response The response to return, never null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
    }
}
