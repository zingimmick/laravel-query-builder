<?php

declare(strict_types=1);

use Zing\QueryBuilder\Samples\CodeSample;
use Zing\QueryBuilder\Samples\IOSample;

return new CodeSample(
    __DIR__ . '/code.php',
    [
        new IOSample(
            '/api/users?is_visible=true',
            'select * from "users" where "is_visible" = true limit 16 offset 0'
        ),
        new IOSample(
            '/api/users?is_visible=true,false',
            'select * from "users" where "is_visible" in (true, false) limit 16 offset 0'
        ),
    ]
);
