<?php

namespace FlexAuth\Type\JWT;

use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DefaultJWTUserFactory
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class DefaultJWTUserFactory implements JWTUserFactoryInterface
{
    public function createFromPayload($payload): UserInterface
    {
        return new User($payload['username'], null, $payload['roles']);
    }
}
