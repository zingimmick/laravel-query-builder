<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Models;

use Jenssegers\Mongodb\Eloquent\HybridRelations;
use Jenssegers\Mongodb\Eloquent\Model;

class Subject extends Model
{
    use HybridRelations;

    protected $connection = 'mongodb';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
