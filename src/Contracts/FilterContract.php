<?php

namespace MorningTrain\Laravel\Filters\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface FilterContract
{

    /**
     * @return mixed
     */
    public function getMetadata();

    /**
     * @param Builder $query
     * @param Request $request
     * @return mixed
     */
    public function apply(Builder $query, Request $request = null);

}
