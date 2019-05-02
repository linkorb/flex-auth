<?php

namespace FlexAuth\Type\JWT;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;

/**
 * Class JWTToken
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class JWTToken extends AbstractToken implements GuardTokenInterface
{
    /** @var string */
    protected $rawToken;
    /** @var string */
    protected $providerKey;

    public function __construct(UserInterface $user, $rawToken, array $roles = [], $providerKey = null)
    {
        parent::__construct($roles);
        if ($user) {
            $this->setUser($user);
        }
        $this->rawToken = $rawToken;
        $this->setAuthenticated(true);
        $this->providerKey = $providerKey;
    }

    public function getCredentials()
    {
        return $this->rawToken;
    }
}