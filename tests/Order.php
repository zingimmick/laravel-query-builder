<?php

namespace Zing\QueryBuilder\Tests;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
