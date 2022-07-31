<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait NestedRelation
{
    /**
     * @return array{string, string}
     */
    protected function resolveNestedRelation(string $property): array
    {
        return collect(explode('.', $property))
            ->pipe(
                static function (Collection $parts): array {
                    return [
                        $parts->except([\count($parts) - 1])
                            ->map(static function (string $value): string {
                                return Str::camel($value);
                            })->implode('.'),
                        $parts->last(),
                    ];
                }
            );
    }
}
