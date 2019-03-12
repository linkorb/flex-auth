<?php

namespace FlexAuth\Security;

use FlexAuth\AuthFlexTypeProviderInterface;
use Symfony\Component\Security\Core\Encoder\Argon2iPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\Pbkdf2PasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

/**
 * Class FlexAuthPasswordEncoder
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class FlexAuthPasswordEncoder implements PasswordEncoderInterface
{
    /** @var AuthFlexTypeProviderInterface */
    protected $flexAuthTypeProvider;
    /** @var PasswordEncoderInterface|null */
    protected $defaultEncoder;

    private $encoders = [
        'plaintext' => PlaintextPasswordEncoder::class,
        'pbkdf2' => Pbkdf2PasswordEncoder::class,
        'bcrypt' => BCryptPasswordEncoder::class,
        'argon2i' => Argon2iPasswordEncoder::class,
    ];

    public function __construct(
        AuthFlexTypeProviderInterface $flexAuthTypeProvider,
        PasswordEncoderInterface $defaultEncoder = null
    ) {
        $this->flexAuthTypeProvider = $flexAuthTypeProvider;
        $this->defaultEncoder = $defaultEncoder;
    }

    /**
     * @param string $raw
     * @param string $salt
     * @return string
     * @throws \Exception
     */
    public function encodePassword($raw, $salt)
    {
        return $this->getEncoder()->encodePassword($raw, $salt);
    }

    /**
     * @param string $encoded
     * @param string $raw
     * @param string $salt
     * @return bool
     * @throws \Exception
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $this->getEncoder()->isPasswordValid($encoded, $raw, $salt);
    }

    public function addEncoder(string $name, string $className)
    {
        if (isset($this->encoders[$name])) {
            throw new \InvalidArgumentException(sprintf('Encoder "%s" is supported already'));
        }

        $this->encoders[$name] = $className;
    }

    private function getEncoder(): PasswordEncoderInterface
    {
        $params = $this->flexAuthTypeProvider->provide();
        $encoder = $params['encoder'] ?? null;
        if (!$encoder) {
            if (!$this->defaultEncoder) {
                throw new \Exception("No flex auth password encoder");
            }

            return $this->defaultEncoder;
        } else {
            if (!isset($this->encoders[$encoder])) {
                throw new \Exception(sprintf('Encoder "%s" is not supported'));
            } else {
                // TODO improve and inject FlexAuthEncoderFactory?!
                $className = $this->encoders[$encoder];
                return new $className();
            }
        }
    }
}