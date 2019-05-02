<?php

namespace FlexAuth;

use FlexAuth\Type\InvalidParamsException;
use FlexAuth\Type\NullUserProvider;
use FlexAuth\Type\UserProviderFactoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProviderFactory
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class UserProviderFactory
{
    /** @var FlexAuthTypeProviderInterface */
    protected $flexAuthTypeProvider;
    /** @var UserProviderRegistry */
    protected $userProviderRegistry;

    public function __construct(FlexAuthTypeProviderInterface $flexAuthTypeProvider, UserProviderRegistry $userProviderRegistry)
    {
        $this->flexAuthTypeProvider = $flexAuthTypeProvider;
        $this->userProviderRegistry = $userProviderRegistry;
    }

    /**
     * @return UserProviderInterface
     * @throws \Exception
     */
    public function create(): UserProviderInterface
    {
        $result = $this->resolveTypeAndParams();
        $type = $result[0];
        $params = $result[1];

        if (!array_key_exists($type, $this->factories)) {
            throw new \InvalidArgumentException(sprintf('Auth type "%s" is not supported', $type));
        }

        $factory = $this->userProviderRegistry->get($type);
        $userProvider = $factory->create($params);

        if ($userProvider === null) {
            $userProvider = new NullUserProvider();
        }

        return $userProvider;
    }

    /**
     * Resolve rype and params from env string
     * @return array
     * @throws \Exception
     */
    private function resolveTypeAndParams()
    {
        $flexAuthData = $this->flexAuthTypeProvider->provide();
        $allowTypes = $this->userProviderRegistry->getExistTypes();

        if (is_null($flexAuthData['type'])) {
            throw new \InvalidArgumentException();
        }

        $type = $flexAuthData['type'];

        if (!in_array($type, $allowTypes)) {
            throw new InvalidParamsException(
                sprintf('Unsupported flex auth environment format. Allow: %s', join(', ', $allowTypes))
            );
        }

        $params = $flexAuthData;
        unset($params['type']);

        return [$type, $params];
    }
}