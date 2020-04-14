<?php

namespace Zing\QueryBuilder\Tests;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function d()
    {
        return $this->belongsTo(self::class);
    }
}
