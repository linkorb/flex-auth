<?php

namespace FlexAuthTest;

use FlexAuth\Type\Chain\ChainUserProviderFactory;
use FlexAuth\TypeProvider\AuthenticatedTypeProvider;
use FlexAuth\TypeProvider\SimpleTypeProvider;
use FlexAuth\UserProviderFactory;
use FlexAuth\UserProviderRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
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
    public function testAuthenticate()
    {
        $eventDispatcher = new EventDispatcher();

        $registry = new UserProviderRegistry();
        $registry->addType(MemoryUserProviderFactory::TYPE, new MemoryUserProviderFactory());
        $registry->addType(JWTUserProviderFactory::TYPE, new JWTUserProviderFactory());
        $registry->addType(ChainUserProviderFactory::TYPE, new ChainUserProviderFactory($registry, $eventDispatcher));

        $typeProvider = new SimpleTypeProvider();
        $userFactory = new UserProviderFactory($typeProvider, $registry);

        $contextProvider = new AuthenticatedTypeProvider($eventDispatcher);

        $providerKey = 'secured_area';

        $daoProvider = new DaoAuthenticationProvider(new FlexUserProvider($userFactory), new UserChecker(),
            $providerKey,
            new EncoderFactory([
                UserInterface::class => new FlexAuthPasswordEncoder($contextProvider)
            ])
        );

        $username = 'alice';
        $password = '4l1c3';

        $users = [
            $username => [
                'password' => $password
            ],
            'bob' => [
                'password' => 'b0b'
            ]
        ];

        $roles = ['ROLE_ADMIN','ROLE_EXAMPLE'];

        $usersLine = [];
        foreach ($users as $username => $data) {
            $password = $data['password'];
            $usersLine[] = "$username:$password:" . implode(";", $roles);
        }


        $validToken = new UsernamePasswordToken($username, $password, $providerKey, $roles);

        $memoryUserParams = [
            'encoder' => 'plain',
            'users' => implode(",", $usersLine)
        ];

        /*$typeProvider->params = [
            'type' => MemoryUserProviderFactory::TYPE,
        ] + $memoryUserParams;

        $this->assertNotNull($daoProvider->authenticate($validToken));
*/
        $typeProvider->params = [
            'type' => 'chain',
            //'encoder' => 'plain',
            'types' => [
                MemoryUserProviderFactory::TYPE => $memoryUserParams
            ]
        ];

        $this->assertNotNull($daoProvider->authenticate($validToken));

        $this->expectException(BadCredentialsException::class);
        $daoProvider->authenticate(new UsernamePasswordToken($username, 'wrong password', $providerKey, $roles));
    }

    /*public function testCreateParamsFromEnvLine()
    {
        $envName = 'FLEX_AUTH';
        $_ENV[$envName] =  . "?users=". implode(",", $usersLine) ."&encoder=$encoder";
        $typeProvider = FlexAuthTypeProviderFactory::fromEnv($envName);
    }*/
}