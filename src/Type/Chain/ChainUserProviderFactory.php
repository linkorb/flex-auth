<?php

namespace FlexAuth\Type\Chain;

use FlexAuth\Type\UserProviderFactoryInterface;
use FlexAuth\UserProviderRegistry;
use Symfony\Component\Security\Core\User\ChainUserProvider;

/**
 * Class ChainUserProviderFactory
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class ChainUserProviderFactory implements UserProviderFactoryInterface
{
    const TYPE = 'chain';

    private $userProviderRegistry;

    public function __construct(UserProviderRegistry $userProviderRegistry)
    {
        $this->userProviderRegistry = $userProviderRegistry;
    }

    public function create($params)
    {
        $userProviders = [];
        foreach ($params as $name => $options)
        {
            $factory = $this->userProviderRegistry->get($name);
            if (!$factory) {
                throw new \InvalidArgumentException();// type not exists
            }

            $userProviders[] = $factory->create($options);
        }

        return new ChainUserProvider($userProviders);
    }
}