<?php

namespace FlexAuth\Type\UserbaseClient;


use FlexAuth\Type\UserProviderFactoryInterface;
use UserBase\Client\UserProvider;
use UserBase\Client\Client;

class UserbaseClientUserProviderFactory implements UserProviderFactoryInterface
{
    public const TYPE = 'userbase';

    public function create($params)
    {
        $client = new Client(
            $params["url"],
            array_key_exists('usersname', $params) ? $params["username"] : null,
            array_key_exists('password', $params) ? $params["password"] : null
        );

        return new UserProvider($client);
    }
}