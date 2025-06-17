<?php

namespace DevPayr\Contracts;

/**
 * Interface InjectableProcessorInterface
 *
 * Allows for custom processing of SDK injectables (e.g. decrypt, verify, and save elsewhere).
 */
interface InjectableProcessorInterface
{
    /**
     * Handle a single injectable payload.
     *
     * @param array $injectable Raw injectable data from API
     * @param string $secret Shared secret (typically the license key)
     * @param string $basePath Base path for writing (if file-based)
     * @param bool $verifySignature Whether to verify HMAC signature
     *
     * @return string Path or identifier of the saved injectable
     */
    public static function handle(array $injectable, string $secret, string $basePath, bool $verifySignature = true): string;
}
