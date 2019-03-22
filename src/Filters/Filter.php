<?php

namespace MorningTrain\Laravel\Filters\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use MorningTrain\Laravel\Filters\Contracts\FilterContract;
use MorningTrain\Laravel\Support\Traits\StaticCreate;

class Filter implements FilterContract
{
    use StaticCreate;

    const PROVIDE_ALL = 'all';
    const PROVIDE_DEFAULT = 'default';

    /**
     * Case providers
     *
     * @var array
     */

    protected $default_values = [];
    protected $providers = [];

    public function when($keys, Closure $closure)
    {
        $this->providers[] = [
            'keys' => (array)$keys,
            'apply' => $closure
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
                    $provider_keys = array_flatten($provider_keys);
                }
                $keys = array_merge($keys, $provider_keys);
            }
        }

        return $keys;
    }

    public function always(Closure $closure)
    {
        return $this->when([], $closure);
    }

    public function getMetadata()
    {
        return [];
    }

    protected function getArguments(array $keys, Request $request = null)
    {
        if (empty($keys)) {
            return [];
        } else {
            if (is_null($request)) {
                return false;
            }
        }

        $args = [];
        foreach ($keys as $key) {
            if (!$request->has($key)) {
                if (isset($this->default_values[$key])) {
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

            if (is_array($args)) {
                $provider['apply'] ($query, ...$args);
            }
        }
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

        if (isset($this->default_values[$key])) {
            return $this->default_values[$key];
        }

        return $default;
    }


}
