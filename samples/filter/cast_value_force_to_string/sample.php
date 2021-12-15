<?php

declare(strict_types=1);

use Zing\QueryBuilder\Samples\CodeSample;
use Zing\QueryBuilder\Samples\IOSample;

return new CodeSample(__DIR__ . '/code.php', [
    new IOSample(
        '/api/orders?content=code,and',
        'select * from "orders" where "content" like "%code,and%" limit 16 offset 0'
    ),
]);
