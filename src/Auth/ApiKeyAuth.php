<?php

namespace DevPayr\Auth;

use DevPayr\Config\Config;

class ApiKeyAuth
{
    /**
     * Returns headers required for API key authentication.
     *
     * @param Config $config
     * @return array
     */
    public static function headers(Config $config): array
    {
        return [
            'X-Api-Key' => $config->get('api_key'),
        ];
    }
}
