<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

class QueryConfiguration
{
    /**
     * @phpstan-var non-empty-string
     *
     * @var string
     */
    private static $delimiter = ',';

    /**
     * @var int
     */
    private static $perPage = 15;

    /**
     * @var string
     */
    private static $pageName = 'per_page';

    public static function getDelimiter(): string
    {
        return self::$delimiter;
    }

    /**
     * @phpstan-param non-empty-string $delimiter
     */
    public static function setDelimiter(string $delimiter): void
    {
        self::$delimiter = $delimiter;
    }

    public static function getPerPage(): int
    {
        return self::$perPage;
    }

    public static function setPerPage(int $perPage): void
    {
        self::$perPage = $perPage;
    }

    public static function getPageName(): string
    {
        return self::$pageName;
    }

    public static function setPageName(string $pageName): void
    {
        self::$pageName = $pageName;
    }
}
