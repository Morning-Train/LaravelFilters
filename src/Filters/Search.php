<?php

namespace MorningTrain\Laravel\Filters\Filters;

class Search extends Filter
{

    public function methods($methods, $pattern = '%%s%')
    {
        // convert keys to array
        if (!is_array($methods)) {
            $methods = [$methods];
        }

        // escape sprintf wildcards
        $pattern = preg_replace('/%([^s])/', '%%$1', $pattern);
        $pattern = preg_replace('/([s])%$/', '$1%%', $pattern);

        return $this->when('search', function ($query) use ($methods, $pattern) {
            $value = array_slice(func_get_args(), 1)[0];
            //$search = sprintf($pattern, $value);
            $search = $value;

            return $query->where(function ($q) use ($methods, $search) {
                foreach ($methods as $index => $method) {
                    if ($index === 0) {
                        return $q->where(function ($q) use ($method, $search) {
                            $q->{$method}($search);
                        });
                    } else {
                        return $q->orWhere(function ($q) use ($method, $search) {
                            $q->{$method}($search);
                        });
                    }
                }
            });
        });
    }

    public function search($keys, $pattern = '%%s%')
    {
        // convert keys to array
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        // escape sprintf wildcards
        $pattern = preg_replace('/%([^s])/', '%%$1', $pattern);
        $pattern = preg_replace('/([s])%$/', '$1%%', $pattern);

        return $this->when('search', function ($query) use ($keys, $pattern) {
            $value = array_slice(func_get_args(), 1)[0];
            $search = sprintf($pattern, $value);

            return $query->where(function ($q) use ($keys, $search) {
                $this->applySearchToKeys($q, $keys, $search);
            });
        });
    }

    public function searchDate($keys, $pattern = '%%s%')
    {
        // convert keys to array
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        // escape sprintf wildcards
        $pattern = preg_replace('/%([^s])/', '%%$1', $pattern);
        $pattern = preg_replace('/([s])%$/', '$1%%', $pattern);

        return $this->when('search', function ($query) use ($keys, $pattern) {
            $value = array_slice(func_get_args(), 1)[0];

            $re = '/(\d*)./m';

            preg_match_all($re, $value . "-", $matches, PREG_SET_ORDER, 0);

            $searchstring = "";

            foreach ($matches as $match) {
                $searchstring .= substr($match[0], 0, -1) . "-";
            }

            $value = substr($searchstring, 0, -1);


            $search = sprintf($pattern, $value);

            return $query->where(function ($q) use ($keys, $search) {
                $this->applySearchToKeys($q, $keys, $search);
            });
        });
    }

    public function applySearchToKeys(&$q, $keys, $search)
    {
        if (is_array($keys) && !empty($keys)) {

            $keys_for_keys = array_keys($keys);

            foreach ($keys as $i => $key) {

                $func = ($i === reset($keys_for_keys)) ? 'where' : 'orWhere';

                if (is_array($key)) {
                    $q->{$func . 'Has'}($i, function ($q) use ($key, $search) {
                        $this->applySearchToKeys($q, $key, $search);
                    });
                } else {
                    $q->$func($key, 'LIKE', $search);
                }
            }

        }
    }

}

