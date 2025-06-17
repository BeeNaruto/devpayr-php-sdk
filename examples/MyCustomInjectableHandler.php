<?php

use DevPayr\Contracts\InjectableProcessorInterface;

class MyCustomInjectableHandler implements InjectableProcessorInterface
{

    public static function handle(array $injectable, string $secret, string $basePath, bool $verifySignature = true): string
    {
        // TODO: Implement handle() method.
    }
}