<?php

namespace FlexAuth\Type\JWT;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use FlexAuth\TypeProvider\FlexAuthTypeProviderInterface;
use FlexAuth\Type\JWT\Exception\JWTDecodeFailureException;
use FlexAuth\Type\JWT\Exception\JWTTokenExpiredException;

/**
 * Class FlexTypeJWTEncoder
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexTypeJWTEncoder implements EnableJWTEncoderInterface
{
    /** @var FlexAuthTypeProviderInterface */
    private $flexAuthTypeProvider;

    public function __construct(FlexAuthTypeProviderInterface $flexAuthTypeProvider)
    {
        $this->flexAuthTypeProvider = $flexAuthTypeProvider;
    }

    public function encode(array $data)
    {
        return JWT::encode($data, $this->getPrivateKey(), $this->getAlgorithm());
    }

    public function decode($token)
    {
        try {
            $payload = JWT::decode($token, $this->getPublicKey(), [$this->getAlgorithm()]);
        } catch (ExpiredException $e) {
            throw new JWTTokenExpiredException();
        } catch (\UnexpectedValueException $e) {
            throw new JWTDecodeFailureException();
        }

        return $payload;
    }

    public function isEnabled(): bool
    {
        $params = $this->flexAuthTypeProvider->provide();

        return $params['type'] === JWTUserProviderFactory::TYPE;
    }

    private function getAlgorithm()
    {
        $params = $this->flexAuthTypeProvider->provide();

        return array_key_exists('algo', $params) ? $params['algo'] : 'RS256';
    }

    private function getPrivateKey()
    {
        $params = $this->flexAuthTypeProvider->provide();
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
        $params = $this->flexAuthTypeProvider->provide();

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