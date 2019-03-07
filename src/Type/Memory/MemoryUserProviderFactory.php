<?php

namespace FlexAuth\Type\Memory;

use FlexAuth\Type\UserProviderFactoryInterface;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

/**
 * Class MemoryUserProviderFactory
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class MemoryUserProviderFactory implements UserProviderFactoryInterface
{
    const TYPE = 'memory';

    public function create($params)
    {
        if (!array_key_exists('users', $params)) {
            throw new \InvalidArgumentException();
        }

        $users = $this->convertUserString($params['users']);
        return new InMemoryUserProvider($users);
    }

    private function convertUserString($usersString)
    {
        $users = [];
        foreach (explode(",", $usersString) as $user) {
            $properties = explode(":", $user);
            $users[$properties[0]] = [
                'password' => $properties[1],
                'roles' => explode(";", $properties[2]),
            ];
        }

        return $users;
    }
}