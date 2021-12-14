<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Zing\QueryBuilder\Samples\SampleCollector;

class SampleTest extends TestCase
{
    /**
     * @return \Zing\QueryBuilder\Samples\Sample[]
     */
    public function samples(): array
    {
        return (new SampleCollector())->samples();
    }

    /**
     * @return \Iterator<array{string, string, string}>
     */
    public function provideCases(): \Iterator
    {
        foreach ($this->samples() as $sample) {
            foreach ($sample->codeSamples as $codeSample) {
                foreach ($codeSample->ioSamples as $case) {
                    yield [$case->uri, $case->sql, $codeSample->code];
                }
            }
        }
    }

    /**
     * @dataProvider provideCases
     */
    public function testSample(string $uri, string $sql, string $code): void
    {
        $request = Request::create($uri);
        self::assertStringEndsWith($request->path(), (string) parse_url($uri, PHP_URL_PATH));
        DB::listen(function (QueryExecuted $queryExecuted) use ($sql): void {
            self::assertSame(
                $sql,
                sprintf(str_replace('?', '%s', $queryExecuted->sql), ...array_map(function ($value): string {
                    if (is_bool($value)) {
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
