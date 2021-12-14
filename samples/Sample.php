<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Samples;

use function collect;

class Sample
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $subtitle;

    /**
     * @var \Illuminate\Support\Collection|\Zing\QueryBuilder\Samples\CodeSample[]
     */
    public $codeSamples;

    /**
     * @var string
     */
    private $description = '';

    /**
     * @param string $title
     * @param string $subtitle
     * @param \Zing\QueryBuilder\Samples\CodeSample[] $codeSamples
     */
    public function __construct(string $title, string $subtitle, array $codeSamples)
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
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
