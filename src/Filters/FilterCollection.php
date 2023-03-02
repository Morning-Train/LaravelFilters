<?php

namespace MorningTrain\Laravel\Filters\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use MorningTrain\Laravel\Filters\Contracts\FilterContract;
use MorningTrain\Laravel\Support\Traits\StaticCreate;

class FilterCollection implements FilterContract
{
    use StaticCreate;

    /**
     * @var Collection
     */
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = collect($filters);
    }

    public function apply(Builder $query, Request $request = null)
    {
        /** @var Filter $filter */
        foreach ($this->filters as $filter) {
            $filter->apply($query, $request);
        }
    }

    public function getMetadata()
    {
        $metadata = [];

        /** @var Filter $filter */
        foreach ($this->filters as $filter) {
            $meta = $filter->getMetadata();

            if (is_array($meta)) {
                $metadata = array_merge($metadata, $meta);
            }
        }

        return $metadata;
    }

    public function collection()
    {
        return $this->filters;
    }

    public function isFiltering()
    {
        return $this->collection()->some(function($filter) {

            if(method_exists($filter, 'isFiltering')) {
                return $filter->isFiltering();
            }

            return false;
        });
    }

}
