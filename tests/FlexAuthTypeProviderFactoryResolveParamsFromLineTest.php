<?php

namespace FlexAuthTest;

use FlexAuth\FlexAuthTypeProviderFactory;
use PHPUnit\Framework\TestCase;

class FlexAuthTypeProviderFactoryResolveParamsFromLineTest extends TestCase
{
    public function testThrowsWhenLineHasNoParams()
    {
        $this->expectException(\InvalidArgumentException::class);

        FlexAuthTypeProviderFactory::resolveParamsFromLine('entity');
    }

    public function testKeepsTruthyParamsAndDropsFalsyOnes()
    {
        // Empty key and "0" are skipped: the guard is truthiness.
        $params = FlexAuthTypeProviderFactory::resolveParamsFromLine(
            'entity?class=\App\Entities\User&=dropped&port=0'
        );

        $this->assertSame(
            [
                'class' => '\App\Entities\User',
                'type' => 'entity',
            ],
            $params
        );
    }
}
