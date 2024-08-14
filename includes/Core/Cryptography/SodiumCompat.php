<?php

namespace BitCode\FI\Core\Cryptography;

use Exception;
use ParagonIE_Sodium_Compat;

class SodiumCompat
{
    public function __construct()
    {
        if (!class_exists('ParagonIE_Sodium_Compat')) {
            require ABSPATH . WPINC . '/sodium_compat/autoload.php';
            error_log(class_exists('ParagonIE_Sodium_Compat') ? 'Found' : 'ParagonIE\Sodium\Compat not found');
        }
    }

    /**
     * Wrap crypto_aead_*_encrypt() in a drop-dead-simple encryption interface
     *
     * @link https://paragonie.com/b/kIqqEWlp3VUOpRD7
     *
     * @param string $message
     * @param string $key
     *
     * @return string
     */
    public function compatEncrypt($message, $key)
    {
        // cast $message to string
        if (!\is_string($message)) {
            $message = \strval($message);
        }

        // Validate key length
        if (\strlen($key) !== SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES) {
            $key = hash('sha256', $key, true); // Creates a 32-byte key
        }

        $nonce = random_bytes(24); // NONCE = Number to be used ONCE, for each message
        $encrypted = ParagonIE_Sodium_Compat::crypto_aead_xchacha20poly1305_ietf_encrypt(
            $message,
            $nonce,
            $nonce,
            $key
        );

        return $nonce . $encrypted;
    }

    /**
     * Wrap crypto_aead_*_decrypt() in a drop-dead-simple decryption interface
     *
     * @link https://paragonie.com/b/kIqqEWlp3VUOpRD7
     *
     * @param string $message - Encrypted message
     * @param string $key     - Encryption key
     *
     * @throws Exception
     *
     * @return string
     */
    public function compatDecrypt($message, $key)
    {
        $nonceLength = SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES;
        $nonce = mb_substr($message, 0, $nonceLength, '8bit');
        $ciphertext = mb_substr($message, $nonceLength, null, '8bit');

        // Validate key length
        if (\strlen($key) !== SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES) {
            $key = hash('sha256', $key, true); // Creates a 32-byte key
        }

        // Validate nonce length
        if (\strlen($nonce) !== $nonceLength) {
            throw new Exception('Invalid nonce length.');
        }

        $plaintext = ParagonIE_Sodium_Compat::crypto_aead_xchacha20poly1305_ietf_decrypt(
            $ciphertext,
            $nonce,
            $nonce,
            $key
        );

        if (!\is_string($plaintext)) {
            throw new Exception('Decryption failed.');
        }

        return $plaintext;
    }
}
