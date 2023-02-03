<?php

namespace FlexAuth\Type\Chain;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class TriggeredUserProvider
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class TriggeredUserProvider implements UserProviderInterface
{
    public const LOADED_USER_EVENT = 'flex-auth.loaded_user';

    /** @var UserProviderInterface */
    protected $decorated;
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var array */
    protected $typeParams;

    public function __construct(UserProviderInterface $decorated, EventDispatcherInterface $eventDispatcher, array $typeParams)
    {
        $this->decorated = $decorated;
        $this->eventDispatcher = $eventDispatcher;
        $this->typeParams = $typeParams;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->decorated->loadUserByUsername($username);
        $this->eventDispatcher->dispatch(self::LOADED_USER_EVENT, new FlexAuthLoadedUserEvent($user, $this->typeParams));

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->decorated->refreshUser($user);
    }

    public function supportsClass($class)
    {
        return $this->decorated->supportsClass($class);
    }


}