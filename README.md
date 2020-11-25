# Laravel Query Builder

<p align="center">
<a href=""><img src="https://github.com/zingimmick/laravel-query-builder/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://codecov.io/gh/zingimmick/laravel-query-builder"><img src="https://codecov.io/gh/zingimmick/laravel-query-builder/branch/master/graph/badge.svg" alt="Code Coverage" /></a>
<a href="https://packagist.org/packages/zing/laravel-query-builder"><img src="https://poser.pugx.org/zing/laravel-query-builder/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/zing/laravel-query-builder"><img src="https://poser.pugx.org/zing/laravel-query-builder/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/zing/laravel-query-builder"><img src="https://poser.pugx.org/zing/laravel-query-builder/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/zing/laravel-query-builder"><img src="https://poser.pugx.org/zing/laravel-query-builder/license" alt="License"></a>
<a href="https://scrutinizer-ci.com/g/zingimmick/laravel-query-builder"><img src="https://scrutinizer-ci.com/g/zingimmick/laravel-query-builder/badges/quality-score.png" alt="Scrutinizer Code Quality"></a>
<a href="https://github.styleci.io/repos/255621279"><img src="https://github.styleci.io/repos/255621279/shield?branch=master" alt="StyleCI Shield"></a>
<a href="https://codeclimate.com/github/zingimmick/laravel-query-builder/maintainability"><img src="https://api.codeclimate.com/v1/badges/6bd3cbd5bd75b6ec5b2e/maintainability" /></a>
<a href="https://app.fossa.com/projects/git%2Bgithub.com%2Fzingimmick%2Flaravel-query-builder?ref=badge_shield" alt="FOSSA Status"><img src="https://app.fossa.com/api/projects/git%2Bgithub.com%2Fzingimmick%2Flaravel-query-builder.svg?type=shield"/></a>
</p>

> **Requires [PHP 7.2.0+](https://php.net/releases/)**

Require Laravel Query Builder using [Composer](https://getcomposer.org):

```bash
composer require zing/laravel-query-builder --dev
```

## Basic usage

```php
// /api/users?name=Harry
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;
use Zing\QueryBuilder\Filter;

QueryBuilder::fromBuilder(User::class, request())
    ->enableFilters([
        Filter::partial('name')
    ])
    ->simplePaginate();

// /api/users?status=1,2,3
QueryBuilder::fromBuilder(User::class, request())
    ->enableFilters([
        Filter::exact('status')
    ])
    ->simplePaginate();

// /api/users?visible=1
QueryBuilder::fromBuilder(User::class, request())
    ->enableFilters([
        Filter::scope('visible')
    ])
    ->simplePaginate();
```

## License

Laravel Query Builder is an open-sourced software licensed under the [MIT license](LICENSE).

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fzingimmick%2Flaravel-query-builder.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fzingimmick%2Flaravel-query-builder?ref=badge_large)