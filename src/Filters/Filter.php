<?php

namespace MorningTrain\Laravel\Filters\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MorningTrain\Laravel\Filters\Contracts\FilterContract;
use MorningTrain\Laravel\Support\Traits\StaticCreate;
use Illuminate\Support\Str;

class Filter implements FilterContract
{
    use StaticCreate;

    const REQUIRED = false;

    const PROVIDE_ALL = 'all';
    const PROVIDE_DEFAULT = 'default';

    /**
     * Provider Types
     */
    const DEFAULT_TYPE = 'when';
    const MISSING = 'missing';

    /**
     * Case providers
     *
     * @var array
     */

    protected $export_only_defaults = [];
    protected $default_values = [];
    protected $providers = [];

    public function __construct($key = null, Closure $closure = null)
    {
        if ($key !== null) {
            $this->when($key, $closure);
        }
    }

    public function when($keys, Closure $closure = null)
    {
        $this->providers[] = [
            'keys' => (array)$keys,
            'apply' => $this->getFilterMethod($closure),
            'type' => static::DEFAULT_TYPE,
        ];

        return $this;
    }

    public function getAllKeys()
    {
        $keys = [];

        if (!empty($this->providers)) {
            foreach ($this->providers as $provider) {
                $provider_keys = $provider['keys'];
                if (is_array($provider_keys)) {
                    $provider_keys = Arr::flatten($provider_keys);
                }
                $keys = array_merge($keys, $provider_keys);
            }
        }

        return $keys;
    }

    public function always(Closure $closure = null)
    {
        return $this->when([], $closure);
    }

    public function missing($keys, Closure $closure = null)
    {
        $this->providers[] = [
            'keys' => (array)$keys,
            'apply' => $this->getFilterMethod($closure),
            'type' => static::MISSING,
        ];

        return $this;
    }

    protected function getFilterMethod(Closure $closure = null)
    {
        return function ($keys, Builder $query, ...$args) use ($closure) {
            if (isset($this->scope)) {
                return $query->{$this->scope}(...$args);
            }

            if ($closure !== null) {
                return $closure($query, ...$args);
            }

            if (is_array($keys) && !empty($keys)) {
                foreach ($keys as $key) {
                    $query->where($key, $this->getKeyOperator($key), ...$args);
                }
            }

            return $query;
        };
    }

    public function getMetadata()
    {
        return [];
    }

    protected function getArguments(array $keys, Request $request = null)
    {
        if (empty($keys)) {
            return [];
        } else if (is_null($request)) {
            return false;
        }

        $args = [];
        foreach ($keys as $key) {
            if (!$request->has($key)) {

                if ($this->getPlaceholder() !== null && $this->when_placeholder !== null) {
                    $args[] = $this->when_placeholder; /// TODO: Make it possible to apply a callback to when_placeholder to run custom scopes
                } else if (array_key_exists($key, $this->default_values) && !in_array($key, $this->export_only_defaults)) {
                    $args[] = $this->default_values[$key];
                }

                if (empty($args)) {
                    return false;
                }
            }

            $args[] = $request->get($key);
        }

        return $args;
    }

    public function apply(Builder $query, Request $request = null)
    {
        // Apply default providers
        foreach ($this->providers as $provider) {
            $args = $this->getArguments($provider['keys'], $request);

            if (is_array($args) && $provider['type'] === static::DEFAULT_TYPE) {
                $provider['apply'] ($provider['keys'], $query, ...$args);
            } else if (!is_array($args) && $provider['type'] === static::MISSING) {
                $provider['apply'] ($provider['keys'], $query);
            }
        }
    }

    public function required()
    {
        $keys = $this->getAllKeys();

        if (is_array($keys) && !empty($keys)) {
            foreach ($keys as $key) {
                $this->missing($key, function () {
                    abort(404);
                });
            }
        }

        return $this;
    }

    /////////////////////////////////
    /// Operators
    /////////////////////////////////

    protected $operators = [];

    protected function getKeyOperator($key) {

        if(isset($this->operators[$key])) {
            return $this->operators[$key];
        }

        return '=';
    }

    public function operator($operator, $key = null) {

        $keys = ($key !== null)?[$key]:$this->getAllKeys();

        if(is_array($keys) && !empty($keys)) {
            foreach($keys as $key) {
                $this->operators[$key] = $operator;
            }
        }

        return $this;
    }

    /////////////////////////////////
    /// Scopes
    /////////////////////////////////

    protected $scope;

    /**
     * @param string $name
     * @return $this
     */
    public function scope(string $name)
    {
        $this->scope = $name;

        return $this;
    }

    /////////////////////////////////
    /// Defaults
    /////////////////////////////////

    public function defaultValue($value = null, $export_only = false)
    {

        $keys = $this->getAllKeys();

        if (is_array($keys) && !empty($keys)) {
            foreach ($keys as $key) {
                $this->default($key, $value);
                if($export_only === true) {
                    $this->export_only_defaults[] = $key;
                }
            }
        }

        return $this;
    }

    public function default($key, $value = null)
    {
        $this->default_values[$key] = $value;

        return $this;
    }

    public function defaults(array $keys)
    {

        if (is_array($keys) && !empty($keys)) {

            if (!empty($keys)) {
                foreach ($keys as $key_name => $default_value) {
                    $this->default($key_name, $default_value);
                }
            }

            return $this;
        }
    }

    public function getDefaultValue($key, $default = null)
    {

        if (array_key_exists($key, $this->default_values)) {
            return $this->default_values[$key];
        }

        return $default;
    }

    /////////////////////////////////
    /// Exporting getters
    /////////////////////////////////

    protected $label = null;
    protected $placeholder = null;
    protected $when_placeholder = null;

    public function getLabel()
    {
        return $this->label;
    }

    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    public function label($value = null)
    {
        $this->label = $value;

        return $this;
    }

    public function placeholder($value = null, $when_placeholder = null)
    {
        $this->placeholder = $value;
        $this->when_placeholder = $when_placeholder;

        return $this;
    }

    /////////////////////////////////
    /// Exporting
    /////////////////////////////////

    protected function getExportType()
    {
        return null;
    }

    protected function extraExport()
    {
        return [];
    }

    public function export()
    {

        $export = [];

        $keys = $this->getAllKeys();
        if (!empty($keys)) {
            foreach ($keys as $key) {
                $export[$key] = array_merge([
                                                "key" => $key,
                                                "value" => $this->getDefaultValue($key),
                                                "label" => $this->getLabel(),
                                                "placeholder" => $this->getPlaceholder(),
                                                "type" => $this->getExportType(),
                                                "required" => static::REQUIRED,
                                            ], $this->extraExport());
            }
        }

        return $export;
    }

}
