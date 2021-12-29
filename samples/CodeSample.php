<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Samples;

use function collect;

class CodeSample
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var \Illuminate\Support\Collection
     */
    public $ioSamples;

    /**
     * @param \Zing\QueryBuilder\Samples\IOSample[] $ioSamples
     */
    public function __construct(string $code, array $ioSamples)
    {
        $this->code = $code;
        $this->ioSamples = collect($ioSamples);
    }

    public function print(): string
    {
        return sprintf("```php\n%s```" . PHP_EOL, str_replace(
            PHP_EOL . PHP_EOL,
            PHP_EOL . PHP_EOL . $this->ioSamples->map(function ($ioSample): string {
                return implode('', [
                    sprintf('// uri: %s' . PHP_EOL, $ioSample->uri),
                    sprintf('// sql: %s' . PHP_EOL, $ioSample->sql),
                ]);
            })->implode(''),
            str_replace('<?php

declare(strict_types=1);

', '', (string) file_get_contents($this->code))
        ));
    }
}
