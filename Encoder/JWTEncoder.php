<?php

namespace Lexik\Bundle\JWTAuthenticationBundle\Encoder;

use Namshi\JOSE\JWS;

/**
 * JWTEncoder
 *
 * @author Dev Lexik <dev@lexik.fr>
 */
class JWTEncoder implements JWTEncoderInterface
{
    /**
     * @var string
     */
    protected $privateKey;

    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $passPhrase;

    /**
     * @param string $privateKey
     * @param string $publicKey
     * @param string $passPhrase
     */
    public function __construct($privateKey, $publicKey, $passPhrase)
    {
        $this->privateKey = $privateKey;
        $this->publicKey  = $publicKey;
        $this->passPhrase = $passPhrase;
    }

    /**
     * {@inheritdoc}
     */
    public function encode(array $data)
    {
        $jws = new JWS('RS256');
        $jws->setPayload($data);
        $jws->sign($this->getPrivateKey());

        return $jws->getTokenString();
    }

    /**
     * {@inheritdoc}
     */
    public function decode($token)
    {
        try {
            $jws = JWS::load($token);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        if (!$jws->isValid($this->getPublicKey())) {
            return false;
        }

        return $jws->getPayload();
    }

    /**
     * @return bool|resource
     */
    protected function getPrivateKey()
    {
        return openssl_pkey_get_private('file://' . $this->privateKey, $this->passPhrase);
    }

    /**
     * @return resource
     */
    protected function getPublicKey()
    {
        return openssl_pkey_get_public('file://' . $this->publicKey);
    }
}
