<?php

declare(strict_types=1);

use Zing\QueryBuilder\Samples\CodeSample;
use Zing\QueryBuilder\Samples\IOSample;

return new CodeSample(
    __DIR__ . '/code.php',
    [
        new IOSample(
            '/api/users?search=Harry&status=1,2,3&desc=created_at&per_page=10',
            'select * from "users" where ("name" like "%Harry%") and "status" in ("1", "2", "3") order by "created_at" desc limit 11 offset 0'
        ),
    ]
);
