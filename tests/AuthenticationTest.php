<?php

namespace FlexAuthTest;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use FlexAuth\Type\Memory\MemoryUserProviderFactory;
use FlexAuth\Type\JWT\JWTUserProviderFactory;
use FlexAuth\Security\FlexUserProvider;
use FlexAuth\Security\FlexAuthPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class AuthenticationTest
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class AuthenticationTest extends TestCase
{
    public function testAuthenticate() {
        $envName = 'FLEX_AUTH';
        $typeProvider = \FlexAuth\FlexAuthTypeProviderFactory::fromEnv($envName);

        $userFactory = new \FlexAuth\UserProviderFactory($typeProvider);
        $userFactory->addType(MemoryUserProviderFactory::TYPE, new MemoryUserProviderFactory());
        $userFactory->addType(JWTUserProviderFactory::TYPE, new JWTUserProviderFactory());

        $providerKey = 'secured_area';

        $daoProvider = new DaoAuthenticationProvider(
            new FlexUserProvider($userFactory),
            new UserChecker(),
            $providerKey,
            new EncoderFactory([
                UserInterface::class => new FlexAuthPasswordEncoder($typeProvider)
            ])
        );

        $username = 'alice';
        $password = '4l1c3';
        $encoder = 'plain';

        $users = [
            $username => [
                'password' => $password
            ],
            'bob' => [
                'password' => 'b0b'
            ]
        ];

        $usersLine = [];
        $roles = ['ROLE_ADMIN','ROLE_EXAMPLE'];
        foreach ($users as $username => $data) {
            $password = $data['password'];
            $usersLine[] = "$username:$password:" . implode(";", $roles);
        }

        $_ENV[$envName] = MemoryUserProviderFactory::TYPE . "?users=". implode(",", $usersLine) ."&encoder=$encoder";
        $this->assertNotNull($daoProvider->authenticate(new UsernamePasswordToken($username, $password, $providerKey, $roles)));

        $this->expectException(BadCredentialsException::class);
        $daoProvider->authenticate(new UsernamePasswordToken($username, 'wrong password', $providerKey, $roles));
    }
}