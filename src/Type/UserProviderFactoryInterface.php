<?php

namespace FlexAuth\Type;

use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Interface UserProviderFactoryInterface
 */
interface UserProviderFactoryInterface
{
    /**
     * Returns null if flex auth type don't need to have own UserProvider as for JWT
     *
     * @param mixed $params
     * @return UserProviderInterface|null
     * @throws InvalidParamsException
     */
    public function create($params); // TODO type hint : ?UserProviderInterface;
}