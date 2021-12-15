<?php

declare(strict_types=1);

use Zing\QueryBuilder\Samples\CodeSample;
use Zing\QueryBuilder\Samples\IOSample;

return new CodeSample(
    __DIR__ . '/code.php',
    [
        new IOSample(
            '/api/users?search_type=number&search_value=2021',
            'select * from "orders" where "number" like "%2021%" limit 16 offset 0'
        ),
    ]
);
