<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class User extends Model
{
    use HybridRelations;

    protected $connection = 'testing';

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeVisible($query, $visible = true)
    {
        return $query->where('is_visible', $visible);
    }
}
