<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeVisible($query, $visible = true)
    {
        return $query->where('is_visible', $visible);
    }
}
