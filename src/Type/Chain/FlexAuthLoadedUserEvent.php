<?php

namespace FlexAuth\Type\Chain;

use FlexAuth\Type\UserProviderFactoryInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class FlexAuthLoadedUserEvent
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexAuthLoadedUserEvent extends Event
{
    /** @var array */
    protected $typeParams;
    /** @var UserInterface */
    protected $user;

    public function __construct(UserInterface $user, array $typeParams)
    {
        $this->typeParams = $typeParams;
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getTypeParams(): array
    {
        return $this->typeParams;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}