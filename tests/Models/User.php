<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeVisible(Builder $query, bool $visible = true): Builder
    {
        return $query->where('is_visible', $visible);
    }
}
