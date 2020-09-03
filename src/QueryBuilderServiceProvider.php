<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application as Laravel;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as Lumen;

class QueryBuilderServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole() && $this->app instanceof Laravel) {
            $this->publishes(
                [
                    $this->getConfigPath() => config_path('query-builder.php'),
                ],
                'config'
            );
        }
    }

    public function register(): void
    {
        $this->registerConfig();
        $this->app->singleton(
            QueryBuilderFactory::class,
            function () {
                return new QueryBuilderFactory(config('query-builder.builders'));
            }
        );
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/../config/query-builder.php';
    }

    protected function registerConfig(): void
    {
        if ($this->app instanceof Lumen) {
            $this->app->configure('query-builder');
        }

        $this->mergeConfigFrom($this->getConfigPath(), 'query-builder');
    }

    public function provides()
    {
        return [
            QueryBuilderFactory::class,
        ];
    }
}
