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
     * This method handles multiple injectables in batch, verifying their signature,
     * decrypting them, and applying them using their specified injection `mode`.
     *
     * @param array $injectables Each injectable must include slug, content, mode, and target_path
     * @param array $options     [
     *                             'secret' => string (required),
     *                             'path'   => string (base code directory, optional),
     *                             'verify' => bool (default: true)
     *                           ]
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
            $targetPath = $injectable['target_path'] ?? null;
            $encrypted  = $injectable['encrypted_content'] ?? $injectable['content'] ?? null;
            $signature  = $injectable['signature'] ?? null;

            if (!$slug || !$encrypted || !$targetPath) {
                throw new DevPayrException("Injectable must include slug, content, and target_path.");
            }

            if ($verify && $signature && !HashHelper::verifySignature($encrypted, $secret, $signature)) {
                throw new DevPayrException("Signature verification failed for injectable: {$slug}");
            }

            $processor = self::$customProcessor ?? self::class;
            $processor::handle($injectable, $secret, $basePath, $verify);
        }
    }


    /**
     * Default injectable handler.
     *
     * Handles all types of injectables, using a given injection `mode` and `target_path`.
     * This method attempts to find or create the file in the codebase (relative to $basePath)
     * and applies the decrypted content accordingly.
     *
     * 🔹 Supported Modes:
     *  - 'append'         → Adds content to the end of the file.
     *  - 'prepend'        → Adds content to the beginning of the file.
     *  - 'replace'        → Replaces file content entirely.
     *  - 'inject'         → (Reserved for future marker-based injection).
     *  - Others default to 'replace'.
     *
     * 🔹 Behavior:
     *  - If file doesn't exist, it is created with full content.
     *  - For file uploads, the content is written directly.
     *
     * @param array $injectable        Decrypted injectable from API
     * @param string $secret           The decryption key used for content
     * @param string $basePath         Base project root to resolve target_path
     * @param bool $verifySignature    Not used here, assumed verified
     * @return string                  Absolute path of the file written
     *
     * @throws DevPayrException
     */
    public static function handle(array $injectable, string $secret, string $basePath, bool $verifySignature = true): string
    {
        $slug       = $injectable['slug'];
        $targetPath = $injectable['target_path'] ?? null;
        $mode       = $injectable['mode'] ?? 'replace';
        $encrypted  = $injectable['encrypted_content'] ?? $injectable['content'] ?? '';
        $decrypted  = CryptoHelper::decrypt($encrypted, $secret);

        if (!$targetPath) {
            throw new DevPayrException("No target path specified for injectable: {$slug}");
        }

        // Resolve full absolute file path (relative to basePath)
        $fullPath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . trim($targetPath, DIRECTORY_SEPARATOR);

        $directory = dirname($fullPath);
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new DevPayrException("Unable to create directory: {$directory}");
        }

        // If file doesn't exist, treat all modes as replace
        if (!file_exists($fullPath)) {
            if (file_put_contents($fullPath, $decrypted) === false) {
                throw new DevPayrException("Failed to write injectable to: {$fullPath}");
            }

            return $fullPath;
        }

        // File exists — handle based on mode
        $existing = file_get_contents($fullPath);
        if ($existing === false) {
            throw new DevPayrException("Failed to read existing content from: {$fullPath}");
        }

        $content = match ($mode) {
            'append' => $existing . $decrypted,
            'prepend' => $decrypted . $existing,
            default => $decrypted,
        };

        if (file_put_contents($fullPath, $content) === false) {
            throw new DevPayrException("Failed to update injectable at: {$fullPath}");
        }

        return $fullPath;
    }

}
