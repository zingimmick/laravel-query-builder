<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application as Laravel;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as Lumen;

class QueryBuilderServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        if (! $this->app instanceof Laravel) {
            return;
        }

        $this->publishes([
            $this->getConfigPath() => config_path('query-builder.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->registerConfig();
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
}
