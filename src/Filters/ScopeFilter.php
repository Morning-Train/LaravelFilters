<?php

namespace MorningTrain\Laravel\Filters\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class ScopeFilter extends Filter
{
    /**
     * @param $keys
     * @param Closure|null $closure
     * @return ScopeFilter
     */
    public function when($keys, Closure $closure = null)
    {
        return parent::when($keys, $this->getScopeMethod());
    }

    /**
     * @param Closure|null $closure
     * @return ScopeFilter
     */
    public function always(Closure $closure = null)
    {
        return parent::always($this->getScopeMethod());
    }

    /**
     * @param string $name
     * @return $this
     */
    public function scope(string $name)
    {
        $this->scope = $name;

        return $this;
    }

    protected function getScopeMethod()
    {
        return function (Builder $query, ...$args) {
            $query->{$this->scope}(...$args);
        };
    }
}
