<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Samples;

use function collect;

class Sample
{
    /**
     * @var \Illuminate\Support\Collection<int|string, \Zing\QueryBuilder\Samples\CodeSample>
     */
    public $codeSamples;

    private string $description = '';

    /**
     * @param \Zing\QueryBuilder\Samples\CodeSample[] $codeSamples
     */
    public function __construct(
        public string $title,
        public string $subtitle,
        array $codeSamples
    ) {
        $this->codeSamples = collect($codeSamples);
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function print(): string
    {
        $lines = [];
        if ($this->description !== '' && $this->description !== '0') {
            $lines[] = sprintf('**%s**' . PHP_EOL, $this->description);
        }

        $lines[] = $this->codeSamples->map->print()->implode(PHP_EOL);

        return implode(PHP_EOL, $lines);
    }
}
