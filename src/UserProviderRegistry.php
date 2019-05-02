<?php

namespace FlexAuth;

use FlexAuth\Type\UserProviderFactoryInterface;

/**
 * Class UserProviderRegistry
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class UserProviderRegistry
{
    /** @var UserProviderFactoryInterface[] */
    protected $factories = [];

    public function addType($typeKey, UserProviderFactoryInterface $userFactory)
    {
        if (array_key_exists($typeKey, $this->factories)) {
            throw new \InvalidArgumentException(sprintf('Auth type "%s" was added already', $typeKey));
        }

        $this->factories[$typeKey] = $userFactory;
    }

    /**
     * @param $type
     * @return UserProviderFactoryInterface
     */
    public function get($type): UserProviderFactoryInterface
    {
        return $this->factories[$type];
    }

    public function getExistTypes()
    {
        return array_keys($this->factories);
    }
}