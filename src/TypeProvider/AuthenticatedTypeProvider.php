<?php

namespace FlexAuth\TypeProvider;

use FlexAuth\Type\Chain\ChainUserProviderFactory;
use FlexAuth\Type\Chain\FlexAuthLoadedUserEvent;
use FlexAuth\Type\Chain\TriggeredUserProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AuthenticatedTypeProvider
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class AuthenticatedTypeProvider implements FlexAuthTypeProviderInterface
{
    private $typeParams;

    /** @var FlexAuthTypeProviderInterface */
    private $originTypeProvider;

    public function __construct(EventDispatcherInterface $eventDispatcher, FlexAuthTypeProviderInterface $originTypeProvider)
    {
        $eventDispatcher->addListener(TriggeredUserProvider::LOADED_USER_EVENT, [$this, 'onLoadedUser']);
        $this->originTypeProvider = $originTypeProvider;
    }

    public function onLoadedUser(FlexAuthLoadedUserEvent $event)
    {
        $this->typeParams = $event->getTypeParams();
    }

    public function provide(): array
    {
        $params = $this->originTypeProvider->provide();
        if ($params['type'] === ChainUserProviderFactory::TYPE) {
            return $this->typeParams;
        } else {
            return $params;
        }
    }
}