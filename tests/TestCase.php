<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
        $this->withFactories(__DIR__ . '/factories');
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set(
            'database',
            [
                'default' => 'testing',
                'connections' => [
                    'testing' => [
                        'driver' => 'sqlite',
                        'database' => ':memory:',
                        'foreign_key_constraints' => false,
                    ],
                ],
            ]
        );
    }

    protected function setUpDatabase(): void
    {
        DB::connection()->getSchemaBuilder()->create(
            'users',
            function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('name');
                $table->boolean('is_visible')->default(true);
                $table->timestamps();
            }
        );
        DB::connection()->getSchemaBuilder()->create(
            'orders',
            function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->index();
                $table->string('number');
                $table->timestamps();
            }
        );
    }
}
