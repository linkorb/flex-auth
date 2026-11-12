<?php

namespace FlexAuthTest;

use FlexAuth\FlexAuthTypeProviderInterface;
use FlexAuth\Type\JWT\EnableJWTEncoderInterface;
use FlexAuth\Type\JWT\JWTEncoderInterface;
use FlexAuth\Type\JWT\JWTTokenAuthenticator;
use FlexAuth\Type\JWT\JWTUserFactoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

class JWTTokenAuthenticatorSupportsTest extends TestCase
{
    public function testSupportsReturnsFalseWhenEncoderIsDisabled(): void
    {
        $encoder = $this->createMock(EnableJWTEncoderInterface::class);
        $encoder->method('isEnabled')->willReturn(false);

        $request = Request::create('/', 'GET', ['jwt' => 'token'], [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer token',
        ]);

        $this->assertFalse($this->authenticator($encoder)->supports($request));
    }

    public function testSupportsReturnsTrueWhenAuthorizationStartsWithBearer(): void
    {
        $encoder = $this->createMock(JWTEncoderInterface::class);

        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer token',
        ]);

        $this->assertTrue($this->authenticator($encoder)->supports($request));
    }

    public function testSupportsReturnsTrueWhenQueryHasJwtAndHeaderIsNotBearer(): void
    {
        $encoder = $this->createMock(EnableJWTEncoderInterface::class);
        $encoder->method('isEnabled')->willReturn(true);

        $request = Request::create('/', 'GET', ['jwt' => 'token'], [], [], [
            'HTTP_AUTHORIZATION' => 'Basic abc',
        ]);

        $this->assertTrue($this->authenticator($encoder)->supports($request));
    }

    public function testSupportsReturnsFalseWhenRequestHasNoToken(): void
    {
        $encoder = $this->createMock(JWTEncoderInterface::class);

        $this->assertFalse($this->authenticator($encoder)->supports(Request::create('/')));
    }

    private function authenticator(JWTEncoderInterface $encoder): JWTTokenAuthenticator
    {
        return new JWTTokenAuthenticator(
            $this->createMock(JWTUserFactoryInterface::class),
            $encoder,
            $this->createMock(FlexAuthTypeProviderInterface::class)
        );
    }
}
