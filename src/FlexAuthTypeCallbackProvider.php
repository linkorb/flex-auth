<?php

namespace FlexAuth;

/**
 * Class FlexAuthTypeCallbackProvider
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexAuthTypeCallbackProvider implements FlexAuthTypeProviderInterface
{
    /** @var callable */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function provide(): array
    {
        return call_user_func($this->callback);
    }
}