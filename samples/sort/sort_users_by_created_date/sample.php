<?php

declare(strict_types=1);

use Zing\QueryBuilder\Samples\CodeSample;
use Zing\QueryBuilder\Samples\IOSample;

return new CodeSample(__DIR__ . '/code.php', [
    new IOSample(
        '/api/users?desc=created_date',
        'select * from "orders" order by "created_at" desc limit 16 offset 0'
    ),
]);
