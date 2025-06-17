<?php

namespace DevPayr;

use DevPayr\Config\Config;
use DevPayr\Exceptions\DevPayrException;
use DevPayr\Runtime\RuntimeValidator;
use DevPayr\Services\ProjectService;
use DevPayr\Services\LicenseService;
use DevPayr\Services\DomainService;
use DevPayr\Services\InjectableService;
use DevPayr\Services\PaymentService;

/**
 * Class DevPayr
 *
 * 🔹 Primary entry point to the SDK.
 * Handles runtime license validation, injectable processing, and provides access to all core services.
 */
class DevPayr
{
    /**
     * Global configuration instance shared across all services.
     */
    protected static Config $config;

    /**
     * 🔧 Bootstraps the SDK — perform runtime validation, set config, and optionally load injectables.
     *
     * @param array $config Configuration options (license key, base URL, callbacks, etc.)
     * @return void
     * @throws DevPayrException
     */
    public static function bootstrap(array $config): void
    {
        self::$config = new Config($config);

        try {
            // Run runtime license validation and fetch injectables (if enabled)
            if (self::$config->isLicenseMode()) {
                $validator = new RuntimeValidator(self::$config);
                $data = $validator->validate();
            }

            // Call optional user-defined callback after successful boot
            if (is_callable($callback = self::$config->get('onReady'))) {
                $callback($data);
            }

        } catch (DevPayrException $e) {
            // Handle DevPayr-specific exceptions (e.g. license invalid)
            self::handleFailure($e->getMessage(), $config);
        } catch (\Throwable $e) {
            // Handle all other errors gracefully
            self::handleFailure("Unexpected error: " . $e->getMessage(), $config);
        }
    }

    /**
     * Handle license validation failure or SDK bootstrap failure.
     *
     * Supports different failure modes: 'die', 'log', 'redirect', or 'silent'.
     *
     * @param string $message The error message
     * @param array $config The original SDK config
     * @return void
     */
    protected static function handleFailure(string $message, array $config): void
    {

        $mode = $config['invalidBehavior'] ?? 'modal';
        $finalMessage = $config['customInvalidMessage'] ?? $message;

        switch ($mode) {
            case 'redirect':
                $target = $config['redirectUrl'] ?? 'https://devpayr.com/upgrade';
                header("Location: {$target}");
                exit;

            case 'log':
                error_log("[DevPayr] Invalid license: {$finalMessage}");
                break;

            case 'silent':
                // Do nothing silently
                break;

            case 'modal':
            default:
                $customView = $config['customInvalidView'] ?? null;

            $defaultPath = __DIR__. '/resources/views/devpayr/unlicensed.html';

            $htmlPath = $customView ?? $defaultPath;


                if (file_exists($htmlPath)) {
                    $html = file_get_contents($htmlPath);
                    $output = str_replace('{{message}}', htmlspecialchars($finalMessage), $html);
                    echo $output;
                } else {
                    header('Content-Type: text/html; charset=utf-8');
                    echo "<h1>⚠️ Unlicensed Software</h1><p>{$finalMessage}</p>";
                }

                exit;
        }
    }

    /**
     * Access the current global Config instance.
     *
     * @return Config
     */
    public static function config(): Config
    {
        return self::$config;
    }

    // ------------------------------------------------------------------
    // 🔹 Core Services – accessible via DevPayr::serviceName() methods
    // ------------------------------------------------------------------

    /**
     *  Project Management API (list, create, update, delete)
     *
     * @return ProjectService
     * @throws DevPayrException
     */
    public static function projects(): ProjectService
    {
        return new ProjectService(self::$config);
    }

    /**
     * License Key API (issue, revoke, validate, etc.)
     *
     * @return LicenseService
     * @throws DevPayrException
     */
    public static function licenses(): LicenseService
    {
        return new LicenseService(self::$config);
    }

    /**
     * Domain Rules API (restrict usage per domain)
     *
     * @return DomainService
     * @throws DevPayrException
     */
    public static function domains(): DomainService
    {
        return new DomainService(self::$config);
    }

    /**
     * Injectable SDK content API (manage encrypted blobs)
     *
     * @return InjectableService
     * @throws DevPayrException
     */
    public static function injectables(): InjectableService
    {
        return new InjectableService(self::$config);
    }

    /**
     * 💵 Payment Status API (check if license/project has been paid for)
     *
     * @return PaymentService
     * @throws DevPayrException
     */
    public static function payments(): PaymentService
    {
        return new PaymentService(self::$config);
    }
}
