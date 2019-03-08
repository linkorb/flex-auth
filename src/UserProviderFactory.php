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
    /** @var UserProviderFactoryInterface[] */
    protected $factories = [];

    protected $authFlexTypeProvider;

    public function __construct(AuthFlexTypeProviderInterface $authFlexTypeProvider)
    {
        $this->authFlexTypeProvider = $authFlexTypeProvider;
    }

    public function addType($typeKey, UserProviderFactoryInterface $userFactory)
    {
        if (array_key_exists($typeKey, $this->factories)) {
            throw new \InvalidArgumentException(sprintf('Auth type "%s" was added already', $typeKey));
        }

        $this->factories[$typeKey] = $userFactory;
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
        $factory = $this->factories[$type];

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
        $flexAuthData = $this->authFlexTypeProvider->provide();

        $allowTypes = array_keys($this->factories);

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