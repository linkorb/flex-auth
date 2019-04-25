<?php

namespace FlexAuth\Type\JWT;

use FlexAuth\FlexAuthTypeProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class JWTTokenAuthenticator
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class JWTTokenAuthenticator extends AbstractGuardAuthenticator
{
    const TOKEN_HEADER = 'Authorization';
    const TOKEN_PREFIX = 'Bearer ';

    /** @var JWTUserFactoryInterface */
    private $JWTUserFactory;
    /** @var JWTEncoderInterface */
    private $JWTEncoder;
    /** @var FlexAuthTypeProviderInterface */
    private $flexAuthTypeProvider;
    /** @var string|null */
    private $loginUrl;

    public function __construct(
        JWTUserFactoryInterface $JWTUserFactory,
        JWTEncoderInterface $JWTEncoder,
        FlexAuthTypeProviderInterface $flexAuthTypeProvider,
        ?string $loginUrl = null
    )
    {
        $this->JWTUserFactory = $JWTUserFactory;
        $this->JWTEncoder = $JWTEncoder;
        $this->flexAuthTypeProvider = $flexAuthTypeProvider;
        $this->loginUrl = $loginUrl;
    }

    public function supports(Request $request)
    {
        if ($this->JWTEncoder instanceof EnableJWTEncoderInterface && !$this->JWTEncoder->isEnabled()) {
            return false;
        }

        $hasHeader = $request->headers->has(self::TOKEN_HEADER) &&
            strpos($request->headers->get(self::TOKEN_HEADER), self::TOKEN_PREFIX) === 0;

        $hasQuery = $request->query->has('jwt');
        return $hasHeader || $hasQuery;
    }

    public function getCredentials(Request $request)
    {
        $authorization = $request->headers->get(self::TOKEN_HEADER);
        if ($authorization) {
            $token = substr($authorization, strlen(self::TOKEN_PREFIX));
        } else {
            $token = $request->query->get('jwt');
        }
        return $token;
    }

    public function createTokenFromUser(UserInterface $user): string
    {
        $params = $this->flexAuthTypeProvider->provide();
        $userField = $params['user_field'] ?? 'username';
        $roleField = $params['role_field'] ?? 'permissions';

        $user = [
            $userField => $user->getUsername(),
            $roleField => implode(",", $user->getRoles())
        ];

        $encodedPayload = $this->JWTEncoder->encode($user);

        return $encodedPayload;
    }

    public function getUser($credentialsToken, UserProviderInterface $userProvider)
    {
        if (!is_string($credentialsToken)) {
            throw new \InvalidArgumentException(
                sprintf('The first argument of the "%s::%s()" method must be string.', __CLASS__, __METHOD__)
            );
        }

        $params = $this->flexAuthTypeProvider->provide();
        $userField = $params['user_field'] ?? 'username';
        $roleField = $params['role_field'] ?? 'permissions';

        $encodedPayload = $credentialsToken;
        $decodedPayload = $this->JWTEncoder->decode($encodedPayload);

        $user = $this->JWTUserFactory->createFromPayload([
            'username' => $decodedPayload->{$userField},
            'roles' => explode(",", $decodedPayload->{$roleField} ?? '')
        ]);

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $isAcceptHtml = $request->headers->has('Accept') && strpos($request->headers->get('Accept'), 'text/html') !== false;
        if ($this->loginUrl && $isAcceptHtml) {
            return new RedirectResponse($this->loginUrl);
        } else {
            return new Response(sprintf('"%s" header required', self::TOKEN_HEADER), 401);
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {

    }

    public function supportsRememberMe()
    {
        return false;
    }
}
