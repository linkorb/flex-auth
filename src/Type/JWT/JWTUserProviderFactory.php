<?php

namespace FlexAuth\Type\JWT;

use FlexAuth\Type\UserProviderFactoryInterface;

/**
 * Class JWTUserProviderFactory
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class JWTUserProviderFactory implements UserProviderFactoryInterface
{
    const TYPE = 'jwt';

    public function create($params)
    {
        return null;
    }
}