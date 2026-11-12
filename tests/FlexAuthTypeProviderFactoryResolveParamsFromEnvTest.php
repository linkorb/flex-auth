<?php

namespace FlexAuthTest;

use FlexAuth\FlexAuthTypeProviderFactory;
use PHPUnit\Framework\TestCase;

class FlexAuthTypeProviderFactoryResolveParamsFromEnvTest extends TestCase
{
    /** @var array */
    private $savedEnv;

    protected function setUp(): void
    {
        $this->savedEnv = $_ENV;
    }

    protected function tearDown(): void
    {
        $_ENV = $this->savedEnv;
    }

    public function testMissingEnvVariableThrows(): void
    {
        $name = 'FLEX_AUTH_CRAP_DOES_NOT_EXIST';
        unset($_ENV[$name]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Env variable "FLEX_AUTH_CRAP_DOES_NOT_EXIST" is not found');

        FlexAuthTypeProviderFactory::resolveParamsFromEnv($name);
    }

    public function testQueryLineReturnsParsedParams(): void
    {
        $_ENV['FLEX_AUTH_CRAP_LINE'] = 'entity?class=\App\Entities\User&property=username';

        $params = FlexAuthTypeProviderFactory::resolveParamsFromEnv('FLEX_AUTH_CRAP_LINE');

        $this->assertSame(
            [
                'class' => '\App\Entities\User',
                'property' => 'username',
                'type' => 'entity',
            ],
            $params
        );
    }

    public function testPlainTypeKeepsPrefixedEnvKeyLowercasedInFull(): void
    {
        $_ENV = [
            'FLEX_AUTH' => 'entity',
            'FLEX_AUTH_ENTITY_CLASS' => '\App\Entities\User',
            'OTHER' => 'ignored',
        ];

        $params = FlexAuthTypeProviderFactory::resolveParamsFromEnv('FLEX_AUTH');

        // substr($key, 0, strlen($key.'_')) keeps the whole key, then strtolower.
        $this->assertSame(
            [
                'flex_auth_entity_class' => '\App\Entities\User',
                'type' => 'entity',
            ],
            $params
        );
    }
}
