<?php

namespace Zing\QueryBuilder;

class QueryConfiguration
{
    /**
     * @phpstan-var non-empty-string
     * @var string
     */
    private static $delimiter = ',';
    /** @var int */
    private static $perPage = 15;
    /** @var string */
    private static $pageName = 'per_page';

    /**
     * @phpstan-return non-empty-string
     * @return string
     */
    public static function getDelimiter(): string
    {
        return self::$delimiter;
    }

    /**
     * @phpstan-param non-empty-string $delimiter
     * @param string $delimiter
     */
    public static function setDelimiter(string $delimiter): void
    {
        self::$delimiter = $delimiter;
    }

    /**
     * @return int
     */
    public static function getPerPage(): int
    {
        return self::$perPage;
    }

    /**
     * @param int $perPage
     */
    public static function setPerPage(int $perPage): void
    {
        self::$perPage = $perPage;
    }

    /**
     * @return string
     */
    public static function getPageName(): string
    {
        return self::$pageName;
    }

    /**
     * @param string $pageName
     */
    public static function setPageName(string $pageName): void
    {
        self::$pageName = $pageName;
    }


}
