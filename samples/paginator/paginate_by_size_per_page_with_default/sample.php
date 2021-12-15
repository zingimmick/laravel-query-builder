<?php

declare(strict_types=1);

use Zing\QueryBuilder\Samples\CodeSample;
use Zing\QueryBuilder\Samples\IOSample;

return new CodeSample(
    __DIR__ . '/code.php',
    [new IOSample('/api/users?size=', 'select * from "users" limit 6 offset 0')]
);
