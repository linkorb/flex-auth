<?php

namespace FlexAuth\TypeProvider;

/**
 * Class SimpleTypeProvider
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class SimpleTypeProvider implements FlexAuthTypeProviderInterface
{
    public $params;

    public function provide(): array
    {
        return $this->params;
    }
}