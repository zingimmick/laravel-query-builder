<?php

namespace Zing\QueryBuilder\Concerns;

trait WithCasts
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Determine whether an attribute should be cast to a native type.
     *
     * @param string $key
     * @param array|string|null $types
     *
     * @return bool
     */
    public function hasCast($key, $types = null)
    {
        if (array_key_exists($key, $this->getCasts())) {
            return $types ? in_array($this->getCastType($key), (array) $types, true) : true;
        }

        return false;
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        return $this->casts;
    }

    /**
     * Get the type of cast for a model attribute.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getCastType($key)
    {
        return $this->getCasts()[$key];
    }

    public function mergeCasts($casts)
    {
        $this->casts = array_merge($this->casts, $casts);
    }

    public function withCasts($casts)
    {
        $this->mergeCasts($casts);

        return $this;
    }

    abstract protected function castAttribute($key, $value);
}
