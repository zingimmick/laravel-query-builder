<?php

declare(strict_types=1);

use Zing\QueryBuilder\Samples\CodeSample;
use Zing\QueryBuilder\Samples\IOSample;

return new CodeSample(
    __DIR__ . '/code.php',
    [
        new IOSample(
            '/api/users?search=Harry',
            'select * from "users" where ("name" like "%Harry%" or "email" like "%Harry%") limit 16 offset 0'
        ),
    ]
);
