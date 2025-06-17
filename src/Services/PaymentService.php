<?php

namespace DevPayr\Services;

use DevPayr\Config\Config;
use DevPayr\Http\HttpClient;
use DevPayr\Exceptions\DevPayrException;
use DevPayr\Exceptions\ApiResponseException;

/**
 * Class PaymentService
 *
 * Verifies if a project has an active payment/subscription attached.
 */
class PaymentService
{
    protected Config $config;
    protected HttpClient $http;

    /**
     * @throws DevPayrException
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->http   = new HttpClient($config);
    }

    /**
     * Check payment status using an API key (project-scoped).
     *
     * @param string|int $projectId
     * @param array $queryParams Optional query parameters
     * @return array
     * @throws ApiResponseException|DevPayrException
     */
    public function checkWithApiKey(string|int $projectId, array $queryParams = []): array
    {
        return $this->http->get("project/{$projectId}/has-paid", $queryParams);
    }

    /**
     * Check payment status using a license key (auto-detects bound project).
     *
     * @param array $queryParams Optional query parameters
     * @return array
     * @throws ApiResponseException|DevPayrException
     */
    public function checkWithLicenseKey(array $queryParams = []): array
    {
        return $this->http->post("project/has-paid", $queryParams);
    }
}
