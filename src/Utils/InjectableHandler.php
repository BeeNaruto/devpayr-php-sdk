<?php

namespace DevPayr\Utils;

use DevPayr\Contracts\InjectableProcessorInterface;
use DevPayr\Exceptions\DevPayrException;

/**
 * Class InjectableHandler
 *
 * Handles injectable processing with optional override support.
 */
class InjectableHandler
{
    protected static ?string $customProcessor = null;

    /**
     * Allow user to set a custom injectable processor.
     *
     * @param string $className Must implement InjectableProcessorInterface
     * @return void
     * @throws DevPayrException
     */
    public static function setProcessor(string $className): void
    {
        if (!class_exists($className) || !is_subclass_of($className, InjectableProcessorInterface::class)) {
            throw new DevPayrException("Custom injectable processor must implement InjectableProcessorInterface.");
        }

        self::$customProcessor = $className;
    }

    /**
     * Process and write injectables to disk (or via custom handler).
     *
     * @param array $injectables
     * @param array $options ['secret', 'path', 'verify']
     * @return void
     * @throws DevPayrException
     */
    public static function process(array $injectables, array $options): void
    {
        $secret   = $options['secret'] ?? null;
        $basePath = $options['path'] ?? sys_get_temp_dir();
        $verify   = $options['verify'] ?? true;

        if (!$secret) {
            throw new DevPayrException("Injectable handler requires a secret key.");
        }

        foreach ($injectables as $injectable) {
            $slug       = $injectable['slug'] ?? null;
            $targetPath = $injectable['target_path'] ?? '';
            $encrypted  = $injectable['encrypted_content'] ?? $injectable['content'] ?? null;
            $signature  = $injectable['signature'] ?? null;

            if (!$slug || !$encrypted) {
                throw new DevPayrException("Missing 'slug' or 'content' in injectable.");
            }

            if ($verify && $signature && !HashHelper::verifySignature($encrypted, $secret, $signature)) {
                throw new DevPayrException("Signature verification failed for injectable: {$slug}");
            }

            $processor = self::$customProcessor ?? self::class;
            $processor::handle($injectable, $secret, $basePath, $verify);
        }
    }

    /**
     * Default injectable handler (writes to disk).
     *
     * @param array $injectable
     * @param string $secret
     * @param string $basePath
     * @param bool $verifySignature
     * @return string
     * @throws DevPayrException
     */
    public static function handle(array $injectable, string $secret, string $basePath, bool $verifySignature = true): string
    {
        $slug       = $injectable['slug'];
        $targetPath = $injectable['target_path'] ?? '';
        $encrypted  = $injectable['encrypted_content'] ?? $injectable['content'];
        $decrypted  = CryptoHelper::decrypt($encrypted, $secret);

        $fullPath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR .
            trim($targetPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR .
            "{$slug}.txt";

        $dir = dirname($fullPath);

        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new DevPayrException("Unable to create directory for injectable: $dir");
        }

        if (file_put_contents($fullPath, $decrypted) === false) {
            throw new DevPayrException("Failed to write injectable to path: $fullPath");
        }

        return $fullPath;
    }
}
