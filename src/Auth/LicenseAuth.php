<?php

namespace DevPayr\Auth;

use DevPayr\Config\Config;

class LicenseAuth
{
    /**
     * Returns headers required for license-based authentication.
     *
     * @param Config $config
     * @return array
     */
    public static function headers(Config $config): array
    {
        return [
            'X-License-Key' => $config->get('license'),
        ];
    }
}
