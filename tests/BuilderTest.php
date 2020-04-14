<?php

namespace Zing\QueryBuilder\Tests;

use Illuminate\Database\Eloquent\Model;
use Zing\QueryBuilder\QueryBuilder;

class BuilderTest extends TestCase
{
    public function test_aa()
    {
        request()->merge(['search' => '1', 'a' => '2']);
        $actual = QueryBuilder::for(User::class, request())
            ->searchable(['b', 'c'])
            ->addFilters('a')
            ->toSql();
        $expected = User::query()
            ->when(request()->input('search'), function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    return $query->orWhere('b', 'like', "%${search}%")
                        ->orWhere('c', 'like', "%${search}%");
                });
            })
            ->when(request()->input('a'), function ($query, $value) {
                return $query->where('a', $value);
            })
            ->toSql();
        self::assertSame($expected, $actual);
    }
}

class User extends Model
{
    public function d()
    {
        return $this->belongsTo(self::class);
    }
}
