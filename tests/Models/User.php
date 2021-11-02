<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 */
class User extends Model
{
    /**
     * @var mixed[]
     */
    protected $guarded = [];

    /**
     * @var string
     */
    protected $connection = 'testing';

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeVisible(Builder $query,bool $visible = true): Builder
    {
        return $query->where('is_visible', $visible);
    }
}
