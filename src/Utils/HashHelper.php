<?php

namespace DevPayr\Utils;

/**
 * Class HashHelper
 *
 * Utility class to generate and verify hashes and HMAC-SHA256 signatures.
 */
class HashHelper
{
    /**
     * Generate a SHA-256 hash of a string.
     *
     * @param string $content
     * @return string
     */
    public static function hash(string $content): string
    {
        return hash('sha256', $content);
    }

    /**
     * Generate an HMAC-SHA256 signature.
     *
     * @param string $content
     * @param string $secret
     * @return string
     */
    public static function signature(string $content, string $secret): string
    {
        return hash_hmac('sha256', $content, $secret);
    }

    /**
     * Check if a string’s hash matches the expected SHA-256 hash.
     *
     * @param string $content
     * @param string $expectedHash
     * @return bool
     */
    public static function verifyHash(string $content, string $expectedHash): bool
    {
        return hash_equals(self::hash($content), $expectedHash);
    }

    /**
     * Check if a string’s HMAC matches the expected HMAC-SHA256.
     *
     * @param string $content
     * @param string $secret
     * @param string $expectedSignature
     * @return bool
     */
    public static function verifySignature(string $content, string $secret, string $expectedSignature): bool
    {
        return hash_equals(self::signature($content, $secret), $expectedSignature);
    }
}
