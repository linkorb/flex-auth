<?php

namespace FlexAuth\Security;

use FlexAuth\FlexAuthTypeProviderInterface;
use FlexAuth\Type\JWT\JWTUserProviderFactory;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ContextListener;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * Allow dynamically determinate firewall is stateless or not.
 * Serve for switch between login via form with session authentication and jwt which stateless
 *
 * Class FlexAuthContextListenerDecorator
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexAuthContextListenerDecorator implements ListenerInterface
{
    /** @var FlexAuthTypeProviderInterface */
    protected $authTypeProvider;

    /** @var ContextListener */
    protected $contextListener;

    protected $statelessTypes = [
        JWTUserProviderFactory::TYPE
    ];
    
    public function __constructor(ContextListener $contextListener, FlexAuthTypeProviderInterface $authTypeProvider)
    {
        $this->contextListener = $contextListener;
        $this->authTypeProvider = $authTypeProvider;
    }

    public function handle(GetResponseEvent $event): void
    {
        if (!in_array($this->authTypeProvider->provide()['type'], $this->statelessTypes, true)) {
            $this->contextListener->handle($event);
        }
    }

    public function addStatelessType(string $type)
    {
        $this->statelessTypes[] = $type;
    }
}