<?php

namespace DevPayr\Config;

use DevPayr\Exceptions\DevPayrException;

class Config
{
    protected array $config;
    protected array $required = [
        'base_url' =>'https://api.devpayr.dev/api/v1/',
        'secret'   => null, // your secret key used for encryption of injectables
    ];
    protected array $defaults = [
        'recheck'            => true,   // Use cache or always revalidate
        'injectables'        => true,    // Fetch & save injectables - if false, injectables will not be returned
        'injectablesVerify'  => true,   // HMAC signature check - should we verify injectable signature
        'injectablesPath'    => null,       // Base Path to inject the injectables - if null, we will utilize system path
        'invalidBehavior'    => 'modal',     // log | modal | redirect | silent
        'redirectUrl'        => null,       // Url to redirect on failure
        'timeout'            => 1000,       // Optional: request timeout in ms
        'action'             => 'check_project',  // Optional action - check official documentation docs.devpayr.com
        'onReady'            => null,   // call back function on success - you will receive successful response here
        'handleInjectables'  => false,  // true | false -- when true, SDK auto-processes the injectables
        'injectablesProcessor'=> null,  // your class which processes the injectables,
        'customInvalidView'  => null,  // optional
        'customInvalidMessage'=> 'This copy is not licensed for production use.',  // optional
        'license'            => null,
        'api_key'            => null,
        'per_page'           => null, // number of list to return
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
