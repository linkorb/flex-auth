<?php

namespace FlexAuth;

/**
 * Interface AuthFlexTypeProviderInterface
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
interface AuthFlexTypeProviderInterface
{
    /**
     * Return type and params as string in format type?param1=value1&param2=value2
     *
     * @example entity?class=\App\Entities\User&property=username
     * @example userbase?dsn=https://username:password@userbase.example.com
     */
    public function provide(): array;
}