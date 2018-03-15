<?php
namespace Zefire\Encryption;

class Encryption
{
    /**
     * Stores the encryption key.
     *
     * @var string
     */
    protected $key;
    /**
     * Stores the cypher.
     *
     * @var string
     */
    protected $cipher;
    /**
     * Stores the initialization vector.
     *
     * @var string
     */
    protected $iv;
    /**
     * Creates a new encryption instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->key = base64_decode(\App::config('app.encryption_key'));
        $this->cipher = \App::config('app.cipher');
        if (!in_array($this->cipher, ['AES-128-CBC', 'AES-256-CBC'])) {
            throw new \Exception($this->cipher . ' cipher is not a valid cipher method with Zefire Framework');
        }
        if (!in_array($this->cipher, openssl_get_cipher_methods())) {
            throw new \Exception($this->cipher . ' cipher is not a valid cipher method for openssl');
        }
        $this->iv = $this->iv();
    }
    /**
     * Encrypts a value.
     *
     * @return string
     */
    public function encrypt($data)
    {
        return base64_encode(openssl_encrypt($data, $this->cipher, $this->key, 0, $this->iv));
    }
    /**
     * Decrypts a value.
     *
     * @return string
     */
    public function decrypt($data)
    {
        return openssl_decrypt(base64_decode($data), $this->cipher, $this->key, 0, $this->iv);
    }
    /**
     * Generates an initialization vector.
     *
     * @return string
     */
    protected function iv()
    {
        return openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
    }
}