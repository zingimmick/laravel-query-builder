<?php

declare(strict_types=1);

use Zing\QueryBuilder\Samples\CodeSample;
use Zing\QueryBuilder\Samples\IOSample;

return new CodeSample(
    __DIR__ . '/code.php',
    [
        new IOSample(
            '/api/users?status=1,2,3',
            'select * from "users" where "status" in ("1", "2", "3") limit 16 offset 0'
        ),
    ]
);
