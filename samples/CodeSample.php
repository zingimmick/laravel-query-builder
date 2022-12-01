<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Samples;

class CodeSample
{
    /**
     * @var \Illuminate\Support\Collection<int|string, \Zing\QueryBuilder\Samples\IOSample>
     */
    public $ioSamples;

    /**
     * @param \Zing\QueryBuilder\Samples\IOSample[] $ioSamples
     */
    public function __construct(
        public string $code,
        array $ioSamples
    ) {
        $this->ioSamples = collect($ioSamples);
    }

    public function print(): string
    {
        return sprintf("```php\n%s```" . PHP_EOL, str_replace(
            PHP_EOL . PHP_EOL,
            PHP_EOL . PHP_EOL . $this->ioSamples->map(static fn ($ioSample): string => implode('', [
                sprintf('// uri: %s' . PHP_EOL, $ioSample->uri),
                sprintf('// sql: %s' . PHP_EOL, $ioSample->sql),
            ]))->implode(''),
            str_replace('<?php

declare(strict_types=1);

', '', (string) file_get_contents($this->code))
        ));
    }
}
