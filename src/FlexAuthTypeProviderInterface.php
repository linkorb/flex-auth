<?php

namespace FlexAuth;

/**
 * Interface FlexAuthTypeProviderInterface
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
interface FlexAuthTypeProviderInterface
{
    /**
     * Provider configuration for runtime which to pass to UserProviderFactoryInterface::create as params
     * 'type' key is required for resolve flex auth type
     *
     * @return array
     */
    public function provide(): array;
}