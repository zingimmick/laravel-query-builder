<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'testing';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
