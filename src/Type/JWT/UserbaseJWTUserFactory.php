<?php declare(strict_types=1);

namespace FlexAuth\Type\JWT;

use Symfony\Component\Security\Core\User\UserInterface;
use UserBase\Client\Model\Account;
use UserBase\Client\Model\AccountUser;
use UserBase\Client\Model\User;

class UserbaseJWTUserFactory implements JWTUserFactoryInterface
{
    public function createFromPayload($payload): UserInterface
    {
        $user = new User($payload['username']);

        $roles = $payload['roles'] ?? [];

        foreach ($roles as $role) {
            $user->addRole($role);
        }

        $user->addAccountUser(
            (new AccountUser())->setAccount(new Account($payload['username']))
        );

        return $user;
    }
}
