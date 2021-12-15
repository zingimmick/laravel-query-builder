<?php

declare(strict_types=1);

use Zing\QueryBuilder\Samples\CodeSample;
use Zing\QueryBuilder\Samples\IOSample;

return new CodeSample(
    __DIR__ . '/code.php',
    [
        new IOSample(
            '/api/users?number=2021&user_name=Jone',
            'select * from "orders" where (("number" like "%2021%") or (exists (select * from "users" where "orders"."user_id" = "users"."id" and "users"."name" like "%Jone%"))) limit 16 offset 0'
        ),
    ]
);
