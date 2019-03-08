<?php

namespace FlexAuth;

/**
 * Interface AuthFlexTypeProviderInterface
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
interface AuthFlexTypeProviderInterface
{
    public function provide(): array;
}