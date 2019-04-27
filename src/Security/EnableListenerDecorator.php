<?php


namespace FlexAuth\Security;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * Class EnableListenerDecorator
 * Allow to switch off listener which was subscribed already
 *
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
abstract class EnableListenerDecorator implements ListenerInterface
{
    /** @var ListenerInterface */
    protected $listener;

    public function __construct(ListenerInterface $listener)
    {
        $this->listener = $listener;
    }

    public function handle(GetResponseEvent $event)
    {
        if ($this->isEnabled()) {
            $this->listener->handle($event);
        }
    }

    abstract public function isEnabled(): bool;
}