<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait NestedRelation
{
    protected function resolveNestedRelation($property)
    {
        return collect(explode('.', $property))
            ->pipe(
                function (Collection $parts): array {
                    return [
                        $parts->except(count($parts) - 1)
                            ->map(function (string $value): string {
                                return Str::camel($value);
                            })->implode('.'),
                        $parts->last(),
                    ];
                }
            );
    }
}
