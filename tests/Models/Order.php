<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Zing\QueryBuilder\Tests\Builders\OrderBuilder;

class Order extends Model
{
    /**
     * @var mixed[]
     */
    protected $guarded = [];

    /**
     * @var string
     */
    protected $connection = 'testing';

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newEloquentBuilder($query): OrderBuilder
    {
        return new OrderBuilder($query);
    }
}
