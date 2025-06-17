<?php

namespace DevPayr\Config;

use DevPayr\Exceptions\DevPayrException;

class Config
{
    protected array $config;
    protected array $required = [
        'base_url' =>'api.devpayr.com/api/v1/',
    ];
    protected array $defaults = [
        'recheck'            => true,
        'injectables'        => true,
        'injectablesVerify'  => true,
        'injectablesPath'    => null,
        'invalidBehavior'    => 'modal',     // log | modal | redirect | silent
        'redirectUrl'        => null,
        'timeout'            => 10,
        'action'             => 'check_project',
        'onReady'            => null,
        'handleInjectables'  => false,  // true | false -- when true, SDK auto-processes the injectables
        'injectablesProcessor'=> null,  // your class which processes the injectables,
        'customInvalidView'  => null,  // optional
        'customInvalidMessage'=> 'This copy is not licensed for production use.',  // optional
        'license'            => null,
        'api_key'            => null,
        'per_page'           => null, // number of list to return
        'include'            =>null,
    ];

    public function __construct(array $userConfig)
    {
        $this->config = array_merge($this->defaults, $userConfig);

        if (! isset($this->config['license']) && ! isset($this->config['api_key'])) {
            throw new DevPayrException('Either "license" or "api_key" must be provided in configuration.');
        }

        foreach ($this->required as $field) {
            if (empty($this->config[$field])) {
                throw new DevPayrException("Missing required config field: {$field}");
            }
        }

        // Normalize trailing slash in base_url
        $this->config['base_url'] = rtrim($this->config['base_url'], '/') . '/';
    }

    /**
     * Get a config value with optional default fallback.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Get full config array (e.g., to pass to other internal classes).
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * Check if a config key is truthy.
     */
    public function isEnabled(string $key): bool
    {
        return !empty($this->config[$key]);
    }

    /**
     * Determine if license mode is enabled.
     */
    public function isLicenseMode(): bool
    {
        return isset($this->config['license']) && !empty($this->config['license']);
    }

    /**
     * Determine if API key mode is enabled.
     */
    public function isApiKeyMode(): bool
    {
        return isset($this->config['api_key']) && !empty($this->config['api_key']);
    }

    /**
     * Get the auth credential (license or api_key).
     */
    public function getAuthCredential(): string
    {
        return $this->config['license'] ?? $this->config['api_key'];
    }
}
