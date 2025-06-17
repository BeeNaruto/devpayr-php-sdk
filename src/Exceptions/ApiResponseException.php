<?php

namespace DevPayr\Exceptions;

/**
 * Class ApiResponseException
 *
 * Thrown when the DevPayr API returns a valid HTTP response
 * with an error status and structured error payload.
 */
class ApiResponseException extends DevPayrException
{
    protected int $statusCode;

    public function __construct(string $message = "", int $statusCode = 500, array $context = [])
    {
        $this->statusCode = $statusCode;

        parent::__construct($message, $statusCode, $context);
    }

    /**
     * Get the HTTP status code returned by the API.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
