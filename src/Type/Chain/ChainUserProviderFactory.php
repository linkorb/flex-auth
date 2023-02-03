<?php

namespace FlexAuth\Type\Chain;

use FlexAuth\Type\UserProviderFactoryInterface;
use FlexAuth\UserProviderRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\ChainUserProvider;

/**
 * Class ChainUserProviderFactory
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class ChainUserProviderFactory implements UserProviderFactoryInterface
{
    const TYPE = 'chain';

    /** @var UserProviderRegistry */
    private $userProviderRegistry;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(UserProviderRegistry $userProviderRegistry, EventDispatcherInterface $eventDispatcher)
    {
        $this->userProviderRegistry = $userProviderRegistry;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create($params)
    {
        $userProviders = [];
        foreach ($params['types'] as $name => $options)
        {
            $factory = $this->userProviderRegistry->get($name);
            if (!$factory) {
                throw new \InvalidArgumentException("'$name' type not exists");
            }

            $userProviders[] = new TriggeredUserProvider($factory->create($options), $this->eventDispatcher, $options + ['type' => $name]);
        }

        return new ChainUserProvider($userProviders);
    }
}