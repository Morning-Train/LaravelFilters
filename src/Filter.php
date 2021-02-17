<?php

namespace MorningTrain\Laravel\Filters;

use MorningTrain\Laravel\Filters\Filters\EnumFilter;
use MorningTrain\Laravel\Filters\Filters\Filter as BaseFilter;
use MorningTrain\Laravel\Filters\Filters\AlwaysFilter;
use MorningTrain\Laravel\Filters\Filters\Pagination;
use MorningTrain\Laravel\Filters\Filters\WithFilter;

class Filter
{

    /**
     * @param string $name
     * @param \Closure $closure
     * @return BaseFilter
     */
    public static function create($name = null, \Closure $closure = null)
    {
        return new BaseFilter($name, $closure);
    }

    /**
     * @param \Closure $closure
     * @return BaseFilter
     */
    public static function always(\Closure $closure = null)
    {
        return new AlwaysFilter($closure);
    }

    /**
     * @param string|array $relations
     * @param string|\Closure|null $callback
     * @return WithFilter
     */
    public static function with($relations, $callback = null)
    {
        return new WithFilter($relations, $callback);
    }

    /**
     * @param $enum
     * @param $constraint
     * @return EnumFilter
     */
    public static function enum($enum, $constraint)
    {
        return new EnumFilter($enum, $constraint);
    }

    /**
     * @return Pagination
     */
    public static function paginate()
    {
        return new Pagination();
    }

}
