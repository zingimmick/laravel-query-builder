<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Concerns;

use Zing\QueryBuilder\Tests\Factory;

trait HasFactory
{
    /**
     * Get a new factory instance for the model.
     *
     * @param mixed $parameters
     *
     * @return \Zing\QueryBuilder\Tests\Factory
     */
    public static function factory(...$parameters)
    {
        return Factory::factoryForModel(static::class)
            ->count(is_numeric($parameters[0] ?? null) ? $parameters[0] : null)
            ->state(is_array($parameters[0] ?? null) ? $parameters[0] : ($parameters[1] ?? []));
    }
}
