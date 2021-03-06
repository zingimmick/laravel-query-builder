<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Zing\QueryBuilder\Tests\Builders\OrderBuilder;

class Order extends Model
{
    protected $guarded = [];

    protected $connection = 'testing';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newEloquentBuilder($query)
    {
        return new OrderBuilder($query);
    }
}
