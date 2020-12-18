<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Zing\QueryBuilder\QueryBuilderServiceProvider;

class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    private const DATABASE = 'database';

    private const TESTING = 'testing';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [QueryBuilderServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set(
            self::DATABASE,
            [
                'default' => self::TESTING,
                'connections' => [
                    self::TESTING => [
                        'driver' => 'sqlite',
                        self::DATABASE => ':memory:',
                        'foreign_key_constraints' => false,
                    ],
                    'mongodb' => [
                        'driver' => 'mongodb',
                        'host' => 'localhost',
                        self::DATABASE => self::TESTING,
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
