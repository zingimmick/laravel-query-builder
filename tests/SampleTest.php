<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Zing\QueryBuilder\Samples\SampleCollector;

/**
 * @internal
 */
final class SampleTest extends TestCase
{
    /**
     * @return \Zing\QueryBuilder\Samples\Sample[]
     */
    public static function samples(): array
    {
        return (new SampleCollector())->samples();
    }

    /**
     * @return \Iterator<array{string, string, string}>
     */
    public static function provideSampleCases(): \Iterator
    {
        foreach (self::samples() as $sample) {
            foreach ($sample->codeSamples as $codeSample) {
                foreach ($codeSample->ioSamples as $case) {
                    yield [$case->uri, $case->sql, $codeSample->code];
                }
            }
        }
    }

    /**
     * @dataProvider provideSampleCases
     */
    public function testSample(string $uri, string $sql, string $code): void
    {
        $request = Request::create($uri);

        /** @var non-empty-string $path */
        $path = $request->path();
        $this->assertStringEndsWith($path, (string) parse_url($uri, PHP_URL_PATH));
        DB::listen(static function (QueryExecuted $queryExecuted) use ($sql): void {
            self::assertSame(
                $sql,
                sprintf(str_replace('?', '%s', $queryExecuted->sql), ...array_map(static function ($value): string {
                    if (\is_bool($value)) {
                        return $value ? 'true' : 'false';
                    }

                    if ($value !== '*') {
                        return '"' . str_replace('"', '""', $value) . '"';
                    }

                    return $value;
                }, $queryExecuted->bindings))
            );
        });

        require $code;
    }
}
