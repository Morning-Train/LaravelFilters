<?php

namespace MorningTrain\Laravel\Filters;

use MorningTrain\Laravel\Filters\Filters\EnumFilter;
use MorningTrain\Laravel\Filters\Filters\Filter as BaseFilter;
use MorningTrain\Laravel\Filters\Filters\AlwaysFilter;
use MorningTrain\Laravel\Filters\Filters\Order;
use MorningTrain\Laravel\Filters\Filters\Pagination;
use MorningTrain\Laravel\Filters\Filters\Search;
use MorningTrain\Laravel\Filters\Filters\WithFilter;
use Illuminate\Support\Traits\Macroable;

class Filter
{

    use Macroable;

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
     * @param string $name
     * @param \Closure $closure
     * @return BaseFilter
     */
    public static function by($name = null, \Closure $closure = null)
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

    /**
     * @return Order
     */
    public static function order()
    {
        return new Order();
    }

    /**
     * @param array|null|mixed $keys
     * @param string $pattern
     * @return Search
     */
    public static function search($keys = null, $pattern = '%%s%')
    {
        $field  = new Search();

        if($keys !== null) {
            $field->search($keys, $pattern);
        }

        return $field;
    }

}
