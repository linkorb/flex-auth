<?php

namespace FlexAuth;

/**
 * Class AuthFlexTypeProviderFactory
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class AuthFlexTypeProviderFactory
{
    public static function fromEnv(string $envVarName)
    {
        return new AuthFlexTypeCallbackProvider(function () use($envVarName) {
            return self::resolveParamsFromEnv($envVarName);
        });
    }

    public static function resolveParamsFromEnv($envVarName)
    {
        if (!array_key_exists($envVarName, $_ENV)) {
            throw new \Exception(sprintf('Env variable "%s" is not found', $envVarName));
        }
        $type = $_ENV[$envVarName];

        try {
            $params = self::resolveParamsFromLine($type);
        } catch (\InvalidArgumentException $e) {
            $params = [];
            foreach ($_ENV as $key => $value) {
                if (strpos($key, $envVarName.'_') === 0) {
                    $paramKey = substr($key, 0, strlen($key.'_'));
                    $params[strtolower($paramKey)] = $value;
                }
            }
            $params['type'] = $type;
        }

        return $params;
    }

    /**
     * @param $line string Type and params as string in format type?param1=value1&param2=value2
     * @example entity?class=\App\Entities\User&property=username
     * @example userbase?dsn=https://username:password@userbase.example.com
     *
     * @return array
     */
    public static function resolveParamsFromLine(string $line): array {
        $parts = [];
        preg_match('/([A-Z0-9_]+)\?((.|\n)+)/i', $line , $parts);

        if (!array_key_exists(2, $parts)) {
            throw new \InvalidArgumentException();
        }

        $stringParams = $parts[2];
        foreach (explode("&", $stringParams) as $keyValue) {
            [$key, $value] = explode("=", $keyValue);
            if ($key && $value) {
                $params[$key] = $value;
            }
        }
        $params['type'] = $parts[1];
        
        return $params;
    }
}