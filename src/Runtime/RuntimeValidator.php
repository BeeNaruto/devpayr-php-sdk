<?php

namespace DevPayr\Runtime;

use DevPayr\Config\Config;
use DevPayr\Exceptions\DevPayrException;
use DevPayr\Http\HttpClient;
use DevPayr\Services\PaymentService;
use DevPayr\Utils\InjectableHandler;

class RuntimeValidator
{
    protected Config $config;
    protected string $license;
    protected string $cacheKey;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->license = $config->get('license');

        if (! $this->license) {
            throw new DevPayrException('License key is required for runtime validation.');
        }

        $this->cacheKey = 'devpayr_' . hash('sha256', $this->license);
    }

    /**
     * Perform license validation and optionally auto-process injectables.
     *
     * @return array Response payload from DevPayr API
     * @throws DevPayrException
     */
    public function validate(): array
    {
        if (! $this->config->get('recheck') && $this->isCached()) {
            return ['cached' => true, 'message' => 'License validated from cache'];
        }

        $response = (new PaymentService($this->config))->checkWithLicenseKey();

        if (! ($response['data']['has_paid'] ?? false)) {
            throw new DevPayrException('Project is unpaid or unauthorized.');
        }

        $this->cacheSuccess();

        // Register custom injectable processor if defined
        if ($processor = $this->config->get('injectablesProcessor')) {
            InjectableHandler::setProcessor($processor);
        }

        // Auto-process injectables if allowed
        if (
            $this->config->get('injectables') &&
            $this->config->get('handleInjectables', true) &&
            !empty($response['data']['injectables'])
        ) {
            $this->handleInjectables($response['data']['injectables']);
        }

        return $response;
    }


    /**
     * Process and write injectables to disk (or delegate to custom handler).
     *
     * @param array $injectables
     * @throws DevPayrException
     */
    protected function handleInjectables(array $injectables): void
    {
        InjectableHandler::process($injectables, [
            'secret' => $this->license,
            'path'   => $this->config->get('injectablesPath', sys_get_temp_dir()),
            'verify' => $this->config->get('injectablesVerify', true),
        ]);
    }

    /**
     * Cache success status to local temp file.
     */
    protected function cacheSuccess(): void
    {
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->cacheKey;
        file_put_contents($file, date('Y-m-d'));
    }

    /**
     * Check if the cache is still valid (based on today's date).
     *
     * @return bool
     */
    protected function isCached(): bool
    {
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->cacheKey;

        return file_exists($file) &&
            trim(file_get_contents($file)) === date('Y-m-d');
    }
}
