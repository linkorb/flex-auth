<?php


namespace FlexAuth\Type\JWT;


use Symfony\Component\Security\Core\User\UserInterface;

interface JWTUserFactoryInterface
{
    public function createFromPayload($payload): UserInterface;
}