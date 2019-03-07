<?php

namespace FlexAuth\Security;

use FlexAuth\UserProviderFactory;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class FlexUserProvider
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexUserProvider implements UserProviderInterface
{
    /**
     * @var UserProviderFactory
     */
    protected $userProviderFactory;

    public function __construct(UserProviderFactory $userProviderFactory)
    {
        $this->userProviderFactory = $userProviderFactory;
    }

    public function loadUserByUsername($username)
    {
        return $this->userProviderFactory->create()->loadUserByUsername($username);
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->userProviderFactory->create()->refreshUser($user);
    }

    public function supportsClass($class)
    {
        return $this->userProviderFactory->create()->supportsClass($class);
    }
}