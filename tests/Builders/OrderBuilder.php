<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModelClass of \Illuminate\Database\Eloquent\Model
 * @extends Builder<TModelClass>
 */
class OrderBuilder extends Builder
{
    public function whereNumberLike(string $number): self
    {
        return $this->where('number', 'like', sprintf('%%%s%%', $number));
    }
}
