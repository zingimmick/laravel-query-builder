<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Closure;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;

abstract class Factory
{
    use ForwardsCalls;

    private const HAS = 'has';

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model;

    /**
     * The number of models that should be generated.
     *
     * @var int|null
     */
    protected $count;

    /**
     * The state transformations that will be applied to the model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $states;

    /**
     * The parent relationships that will be applied to the model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $has;

    /**
     * The child relationships that will be applied to the model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $for;

    /**
     * The "after making" callbacks that will be applied to the model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $afterMaking;

    /**
     * The "after creating" callbacks that will be applied to the model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $afterCreating;

    /**
     * The name of the database connection that will be used to create the models.
     *
     * @var string
     */
    protected $connection;

    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * The default namespace where factories reside.
     *
     * @var string
     */
    protected static $namespace = 'Database\\Factories\\';

    /**
     * The default model name resolver.
     *
     * @var callable
     */
    protected static $modelNameResolver;

    /**
     * The factory name resolver.
     *
     * @var callable
     */
    protected static $factoryNameResolver;

    /**
     * Create a new factory instance.
     *
     * @param int|null $count
     * @param \Illuminate\Support\Collection|null $states
     * @param \Illuminate\Support\Collection|null $has
     * @param \Illuminate\Support\Collection|null $for
     * @param \Illuminate\Support\Collection|null $afterMaking
     * @param \Illuminate\Support\Collection|null $afterCreating
     * @param null $connection
     */
    public function __construct(
        $count = null,
        ?Collection $states = null,
        ?Collection $has = null,
        ?Collection $for = null,
        ?Collection $afterMaking = null,
        ?Collection $afterCreating = null,
        $connection = null
    ) {
        $this->count = $count;
        $this->states = $states ?: new Collection();
        $this->has = $has ?: new Collection();
        $this->for = $for ?: new Collection();
        $this->afterMaking = $afterMaking ?: new Collection();
        $this->afterCreating = $afterCreating ?: new Collection();
        $this->connection = $connection;
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    abstract public function definition();

    /**
     * Get a new factory instance for the given attributes.
     *
     * @param callable|array $attributes
     *
     * @return static
     */
    public static function new($attributes = [])
    {
        return (new static())->state($attributes)->configure();
    }

    /**
     * Get a new factory instance for the given number of models.
     *
     * @param int $count
     *
     * @return static
     */
    public static function times(int $count)
    {
        return static::new()->count($count);
    }

    /**
     * Configure the factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this;
    }

    /**
     * Create a single model and persist it to the database.
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createOne($attributes = [])
    {
        return $this->count(null)->create($attributes);
    }

    /**
     * Create a collection of models and persist them to the database.
     *
     * @param array $attributes
     * @param \Illuminate\Database\Eloquent\Model|null $parent
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        if (! empty($attributes)) {
            return $this->state($attributes)->create([], $parent);
        }

        $results = $this->make($attributes, $parent);

        if ($results instanceof Model) {
            $this->store(collect([$results]));

            $this->callAfterCreating(collect([$results]), $parent);
        } else {
            $this->store($results);

            $this->callAfterCreating($results, $parent);
        }

        return $results;
    }

    /**
     * Set the connection name on the results and store them.
     *
     * @param \Illuminate\Support\Collection $results
     *
     * @return void
     */
    protected function store(Collection $results): void
    {
        $results->each(
            function ($model): void {
                if (! isset($this->connection)) {
                    $model->setConnection($model->newQueryWithoutScopes()->getConnection()->getName());
                }

                $model->save();

                $this->createChildren($model);
            }
        );
    }

    /**
     * Create the children for the given model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    protected function createChildren(Model $model): void
    {
        Model::unguarded(
            function () use ($model): void {
                $this->has->each(
                    function ($has) use ($model): void {
                        $has->createFor($model);
                    }
                );
            }
        );
    }

    /**
     * Make a single instance of the model.
     *
     * @param callable|array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function makeOne($attributes = [])
    {
        return $this->count(null)->make($attributes);
    }

    /**
     * Create a collection of models.
     *
     * @param array $attributes
     * @param \Illuminate\Database\Eloquent\Model|null $parent
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function make($attributes = [], ?Model $parent = null)
    {
        if (! empty($attributes)) {
            return $this->state($attributes)->make([], $parent);
        }

        if ($this->count === null) {
            return tap(
                $this->makeInstance($parent),
                function ($instance): void {
                    $this->callAfterMaking(collect([$instance]));
                }
            );
        }

        if ($this->count < 1) {
            return $this->newModel()->newCollection();
        }

        $instances = $this->newModel()->newCollection(
            array_map(
                function () use ($parent) {
                    return $this->makeInstance($parent);
                },
                range(1, $this->count)
            )
        );

        $this->callAfterMaking($instances);

        return $instances;
    }

    /**
     * Make an instance of the model with the given attributes.
     *
     * @param \Illuminate\Database\Eloquent\Model|null $parent
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function makeInstance(?Model $parent)
    {
        return Model::unguarded(
            function () use ($parent) {
                return tap(
                    $this->newModel($this->getExpandedAttributes($parent)),
                    function ($instance): void {
                        if (isset($this->connection)) {
                            $instance->setConnection($this->connection);
                        }
                    }
                );
            }
        );
    }

    /**
     * Get a raw attributes array for the model.
     *
     * @param \Illuminate\Database\Eloquent\Model|null $parent
     *
     * @return mixed
     */
    protected function getExpandedAttributes(?Model $parent)
    {
        return $this->expandAttributes($this->getRawAttributes($parent));
    }

    /**
     * Get the raw attributes for the model as an array.
     *
     * @param \Illuminate\Database\Eloquent\Model|null $parent
     *
     * @return array
     */
    protected function getRawAttributes(?Model $parent)
    {
        $this->faker = $this->withFaker();

        return $this->states->pipe(
            function ($states) {
                return $this->for->isEmpty() ? $states : new Collection(
                    array_merge(
                        [
                            function () {
                                return $this->parentResolvers();
                            },
                        ],
                        $states->all()
                    )
                );
            }
        )->reduce(
            function ($carry, $state) use ($parent) {
                if ($state instanceof Closure) {
                    $state = $state->bindTo($this);
                }

                return array_merge($carry, $state($carry, $parent));
            },
            $this->definition()
        );
    }

    /**
     * Expand all attributes to their underlying values.
     *
     * @param array $definition
     *
     * @return array
     */
    protected function expandAttributes(array $definition)
    {
        return collect($definition)->map(
            function ($attribute, $key) use (&$definition) {
                if (is_callable($attribute) && ! is_string($attribute) && ! is_array($attribute)) {
                    $attribute = $attribute($definition);
                }

                if ($attribute instanceof self) {
                    $attribute = $attribute->create()->getKey();
                } elseif ($attribute instanceof Model) {
                    $attribute = $attribute->getKey();
                }

                $definition[$key] = $attribute;

                return $attribute;
            }
        )->all();
    }

    /**
     * Add a new state transformation to the model definition.
     *
     * @param callable|array $state
     *
     * @return static
     */
    public function state($state)
    {
        return $this->newInstance(
            [
                'states' => $this->states->concat(
                    [
                        is_callable($state) ? $state :
                    function () use ($state) {
                        return $state;
                    },
                    ]
                ),
            ]
        );
    }

    /**
     * Attempt to guess the relationship name for a "has" relationship.
     *
     * @param string $related
     *
     * @return string
     */
    protected function guessRelationship(string $related)
    {
        $guess = Str::camel(Str::plural(class_basename($related)));

        return method_exists($this->modelName(), $guess) ? $guess : Str::singular($guess);
    }

    /**
     * Add a new "after making" callback to the model definition.
     *
     * @param \Closure $callback
     *
     * @return static
     */
    public function afterMaking(Closure $callback)
    {
        return $this->newInstance(['afterMaking' => $this->afterMaking->concat([$callback])]);
    }

    /**
     * Add a new "after creating" callback to the model definition.
     *
     * @param \Closure $callback
     *
     * @return static
     */
    public function afterCreating(Closure $callback)
    {
        return $this->newInstance(['afterCreating' => $this->afterCreating->concat([$callback])]);
    }

    /**
     * Call the "after making" callbacks for the given model instances.
     *
     * @param \Illuminate\Support\Collection $instances
     *
     * @return void
     */
    protected function callAfterMaking(Collection $instances): void
    {
        $instances->each(
            function ($model): void {
                $this->afterMaking->each(
                    function ($callback) use ($model): void {
                        $callback($model);
                    }
                );
            }
        );
    }

    /**
     * Call the "after creating" callbacks for the given model instances.
     *
     * @param \Illuminate\Support\Collection $instances
     * @param \Illuminate\Database\Eloquent\Model|null $parent
     *
     * @return void
     */
    protected function callAfterCreating(Collection $instances, ?Model $parent = null): void
    {
        $instances->each(
            function ($model) use ($parent): void {
                $this->afterCreating->each(
                    function ($callback) use ($model, $parent): void {
                        $callback($model, $parent);
                    }
                );
            }
        );
    }

    /**
     * Specify how many models should be generated.
     *
     * @param int|null $count
     *
     * @return static
     */
    public function count(?int $count)
    {
        return $this->newInstance(['count' => $count]);
    }

    /**
     * Specify the database connection that should be used to generate models.
     *
     * @param string $connection
     *
     * @return static
     */
    public function connection(string $connection)
    {
        return $this->newInstance(['connection' => $connection]);
    }

    /**
     * Create a new instance of the factory builder with the given mutated properties.
     *
     * @param array $arguments
     *
     * @return static
     */
    protected function newInstance(array $arguments = [])
    {
        return new static(
            ...array_values(
                array_merge(
                    [
                        'count' => $this->count,
                        'states' => $this->states,
                        self::HAS => $this->has,
                        'for' => $this->for,
                        'afterMaking' => $this->afterMaking,
                        'afterCreating' => $this->afterCreating,
                        'connection' => $this->connection,
                    ],
                    $arguments
                )
            )
        );
    }

    /**
     * Get a new model instance.
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function newModel(array $attributes = [])
    {
        $model = $this->modelName();

        return new $model($attributes);
    }

    /**
     * Get the name of the model that is generated by the factory.
     *
     * @return string
     */
    public function modelName()
    {
        $resolver = static::$modelNameResolver ?: function (self $factory) {
            $factoryBasename = Str::replaceLast('Factory', '', class_basename($factory));

            return class_exists('App\\Models\\' . $factoryBasename)
                ? 'App\\Models\\' . $factoryBasename
                : 'App\\' . $factoryBasename;
        };

        return $this->model ?: $resolver($this);
    }

    /**
     * Specify the callback that should be invoked to guess model names based on factory names.
     *
     * @param callable $callback
     *
     * @return void
     */
    public static function guessModelNamesUsing(callable $callback): void
    {
        static::$modelNameResolver = $callback;
    }

    /**
     * Specify the default namespace that contains the application's model factories.
     *
     * @param string $namespace
     *
     * @return void
     */
    public static function useNamespace(string $namespace): void
    {
        static::$namespace = $namespace;
    }

    /**
     * Get a new factory instance for the given model name.
     *
     * @param string $modelName
     *
     * @return static
     */
    public static function factoryForModel(string $modelName)
    {
        $factory = static::resolveFactoryName($modelName);

        return forward_static_call([$factory, 'new'], []);
    }

    /**
     * Specify the callback that should be invoked to guess factory names based on dynamic relationship names.
     *
     * @param callable $callback
     *
     * @return void
     */
    public static function guessFactoryNamesUsing(callable $callback): void
    {
        static::$factoryNameResolver = $callback;
    }

    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Get the factory name for the given model name.
     *
     * @param string $modelName
     *
     * @return string
     */
    public static function resolveFactoryName(string $modelName)
    {
        $resolver = static::$factoryNameResolver ?: function (string $modelName) {
            $modelName = Str::startsWith($modelName, 'App\\Models\\')
                ? Str::after($modelName, 'App\\Models\\')
                : Str::after($modelName, 'App\\');

            return static::$namespace . $modelName . 'Factory';
        };

        return $resolver($modelName);
    }

    /**
     * Proxy dynamic factory methods onto their proper methods.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (! Str::startsWith($method, ['for', self::HAS])) {
            static::throwBadMethodCallException($method);
        }

        $relationship = Str::camel(Str::substr($method, 3));

        $factory = static::factoryForModel(
            get_class($this->newModel()->{$relationship}()->getRelated())
        );
        if (Str::startsWith($method, 'for')) {
            return $this->for($factory->state($parameters[0] ?? []), $relationship);
        }

        if (Str::startsWith($method, self::HAS)) {
            return $this->has(
                $factory
                    ->count(is_numeric($parameters[0] ?? null) ? $parameters[0] : 1)
                    ->state(is_callable($parameters[0] ?? null) || is_array($parameters[0] ?? null) ? $parameters[0] : ($parameters[1] ?? [])),
                $relationship
            );
        }
    }
}
