<?php

namespace FlexAuth\Type\JWT;

/**
 * Interface EnableJWTEncoderInterface
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
interface EnableJWTEncoderInterface extends JWTEncoderInterface
{
    public function isEnabled(): bool;
}