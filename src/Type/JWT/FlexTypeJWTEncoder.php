<?php

namespace FlexAuth\Type\JWT;

use Firebase\JWT\JWT;
use FlexAuth\Security\AuthFlexTypeProviderInterface;

/**
 * Class FlexTypeJWTEncoder
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexTypeJWTEncoder implements EnableJWTEncoderInterface
{
    /** @var AuthFlexTypeProviderInterface */
    private $authFlexTypeProvider;

    public function __construct(AuthFlexTypeProviderInterface $authFlexTypeProvider)
    {
        $this->authFlexTypeProvider = $authFlexTypeProvider;
    }

    public function encode(array $data)
    {
        return JWT::encode($data, $this->getPrivateKey(), $this->getAlgorithm());
    }

    public function decode($token)
    {
        return JWT::decode($token, $this->getPublicKey(), [$this->getAlgorithm()]);
    }

    public function isEnabled(): bool
    {
        $params = $this->authFlexTypeProvider->provide();

        return $params['type'] === JWTUserProviderFactory::TYPE;
    }

    private function getAlgorithm()
    {
        $params = $this->authFlexTypeProvider->provide();

        return array_key_exists('algo', $params) ? $params['algo'] : 'RS256';
    }

    private function getPrivateKey()
    {
        $params = $this->authFlexTypeProvider->provide();
        $privateKey = $params['private_key'];

        $isFilePath = substr($privateKey, 0, 1) === '@';
        if (!$isFilePath) {
            return $privateKey;
        }

        $filePath = substr($privateKey, 1);
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('Private key file "%s" is not exist', $filePath));
        }
        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException(sprintf('Private key file "%s" is not readable', $filePath));
        }

        return file_get_contents($filePath);
    }

    private function getPublicKey()
    {
        $params = $this->authFlexTypeProvider->provide();

        $publicKey = $params['public_key'];
        $isFilePath = substr($publicKey, 0, 1) === '@';
        if (!$isFilePath) {
            return $publicKey;
        }

        $filePath = substr($publicKey, 1);
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('Public key file "%s" is not exist', $filePath));
        }
        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException(sprintf('Public key file "%s" is not readable', $filePath));
        }

        return file_get_contents($filePath);
    }
}