<?php

declare(strict_types=1);

use Zing\QueryBuilder\Samples\CodeSample;
use Zing\QueryBuilder\Samples\IOSample;

return new CodeSample(
    __DIR__ . '/code.php',
    [
        new IOSample(
            '/api/users?search=2021',
            'select * from "users" where ("number" like "%2021%" or ("id" = "2021")) limit 16 offset 0'
        ),
    ]
);
