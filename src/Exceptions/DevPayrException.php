<?php

namespace DevPayr\Exceptions;

use Exception;

/**
 * Class DevPayrException
 *
 * The base exception for all errors thrown by the DevPayr SDK.
 * Other SDK-specific exceptions (like API errors or license failures) extend this class.
 */
class DevPayrException extends Exception
{
    protected array $context = [];

    /**
     * Create a new DevPayrException instance.
     *
     * @param string          $message
     * @param int             $code
     * @param array|Exception $context
     */
    public function __construct(string $message = "", int $code = 0, array|Exception $context = [])
    {
        if ($context instanceof Exception) {
            parent::__construct($message, $code, $context);
        } else {
            $this->context = $context;
            parent::__construct($message, $code);
        }
    }

    /**
     * Get the additional error context (e.g. response body, debug info).
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
