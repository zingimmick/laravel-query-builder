<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Builders;

use Illuminate\Database\Eloquent\Builder;

class OrderBuilder extends Builder
{
    public function whereNumberLike($number)
    {
        return $this->where('number', 'like', "%{$number}%");
    }
}
