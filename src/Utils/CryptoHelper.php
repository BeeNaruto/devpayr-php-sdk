<?php

namespace DevPayr\Utils;

use DevPayr\Exceptions\DevPayrException;

/**
 * Class CryptoHelper
 *
 * Provides AES-256-CBC encryption and decryption using a SHA-256 normalized key.
 * Encrypted format: base64_encode(iv::ciphertext)
 */
class CryptoHelper
{
    /**
     * Decrypt an encrypted string (base64 encoded "iv::cipherText").
     *
     * @param string $encrypted
     * @param string $key
     * @return string
     * @throws DevPayrException
     */
    public static function decrypt(string $encrypted, string $key): string
    {
        $decoded = base64_decode($encrypted, true);
        if ($decoded === false) {
            throw new DevPayrException("Failed to base64-decode encrypted string.");
        }

        [$iv, $cipherText] = explode('::', $decoded, 2) + [null, null];
        if (!$iv || !$cipherText) {
            throw new DevPayrException("Invalid encrypted format — expected 'iv::cipherText'.");
        }

        $normalizedKey = hash('sha256', $key, true);
        $decrypted = openssl_decrypt($cipherText, 'aes-256-cbc', $normalizedKey, 0, $iv);

        if ($decrypted === false) {
            throw new DevPayrException("Decryption failed. Possibly incorrect key or corrupt data.");
        }

        return $decrypted;
    }

    /**
     * Encrypt a plaintext string to base64(iv::cipherText).
     *
     * @param string $plaintext
     * @param string $key
     * @return string
     * @throws DevPayrException
     */
    public static function encrypt(string $plaintext, string $key): string
    {
        $cipher = 'aes-256-cbc';
        $ivLength = openssl_cipher_iv_length($cipher);

        try {
            $iv = random_bytes($ivLength);
        } catch (\Exception $e) {
            throw new DevPayrException("IV generation failed: " . $e->getMessage());
        }

        $normalizedKey = hash('sha256', $key, true);
        $cipherText = openssl_encrypt($plaintext, $cipher, $normalizedKey, 0, $iv);

        if ($cipherText === false) {
            throw new DevPayrException("Encryption failed.");
        }

        return base64_encode($iv . '::' . $cipherText);
    }
}
