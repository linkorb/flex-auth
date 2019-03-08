<?php

namespace FlexAuth\Type\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface JWTUserFactoryInterface
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
interface JWTUserFactoryInterface
{
    public function createFromPayload($payload): UserInterface;
}