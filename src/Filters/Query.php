<?php

namespace MorningTrain\Laravel\Filters\Filters;

class Query extends Filter
{

    public function matches($keys, $operator = '=', $transform = '%s')
    {
        // convert keys to array
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        // escape sprintf wildcards
        $transform = preg_replace('/%([^s])/', '%%$1', $transform);
        $transform = preg_replace('/([s])%$/', '$1%%', $transform);

        return $this->when($keys, function ($query) use ($keys, $operator, $transform) {
            $values = array_slice(func_get_args(), 1);

            return $query->where(function ($query) use ($keys, $values, $operator, $transform) {
                foreach ($keys as $i => $key) {
                    $query->where($key, $operator, sprintf($transform, $values[$i]));
                }
            });
        });
    }

    public function matchesLike($keys, $pattern = '%%s%')
    {
        return $this->matches($keys, 'LIKE', $pattern);
    }

    public function matchesNotLike($keys, $pattern = '%%s%')
    {
        return $this->matches($keys, 'NOT LIKE', $pattern);
    }

}