<?php

namespace Zing\QueryBuilder\Tests;

use Illuminate\Database\Eloquent\Model;
use Zing\QueryBuilder\QueryBuilder;

class BuilderTest extends TestCase
{
    public function test_aa()
    {
        $actual = QueryBuilder::for(User::class, request())
            ->searchable(['b', 'c', 'd.e', 'd.f', 'd.d.f'])
            ->addFilters('a')
            ->toSql();
        $expected = User::query()
            ->when(request()->input('search'), function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    return $query->orWhere('b', 'like', "%${search}%")
                        ->orWhere('c', 'like', "%${search}%")
                        ->orWhereHas('d', function ($query) use ($search) {
                            $query->where(function ($query) use ($search) {
                                return $query->orWhere('e', 'like', "%${search}%")
                                    ->orWhere('f', 'like', "%${search}%");
                            });
                        })
                        ->orWhereHas('d.d', function ($query) use ($search) {
                            $query->where(function ($query) use ($search) {
                                return $query
                                    ->orWhere('f', 'like', "%${search}%");
                            });
                        });
                });
            })
            ->when(request()->input('a'), function ($query, $value) {
                return $query->where('a', $value);
            })
            ->toSql();
        self::assertTrue($expected, $actual);
    }
}

class User extends Model
{
    public function d()
    {
        return $this->belongsTo(self::class);
    }
}