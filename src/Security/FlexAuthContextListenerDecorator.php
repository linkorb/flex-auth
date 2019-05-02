<?php

namespace FlexAuth\Security;

use FlexAuth\FlexAuthTypeProviderInterface;
use Symfony\Component\Security\Http\Firewall\ContextListener;

/**
 * Allow dynamically determinate firewall is stateless or not.
 * Serve for switch between login via form with session authentication and jwt which stateless
 *
 * Class FlexAuthContextListenerDecorator
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexAuthContextListenerDecorator extends EnableListenerDecorator
{
    /** @var FlexAuthTypeProviderInterface */
    protected $authTypeProvider;

    protected $statelessTypes = [];

    public function __construct(ContextListener $contextListener, FlexAuthTypeProviderInterface $authTypeProvider)
    {
        parent::__construct($contextListener);
        $this->authTypeProvider = $authTypeProvider;
    }

    public function isEnabled(): bool
    {
        return !$this->isStateless();
    }

    private function isStateless()
    {
        return in_array($this->authTypeProvider->provide()['type'], $this->statelessTypes, true);
    }

    public function addStatelessType(string $type)
    {
        $this->statelessTypes[] = $type;
    }
}