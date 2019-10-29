<?php

namespace MorningTrain\Laravel\Filters\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class RelationshipFilter
 * @package MorningTrain\Laravel\Filters\Filters
 */
class RelationshipFilter extends SelectFilter
{

    protected function applySelectionToQuery($q, $constraint, $values)
    {
        $q->whereHas($constraint, function(Builder $q) use($values){
            $q->whereIn($this->getSelectionKeyName($q), $values);
        });

        return $q;
    }

    protected function getSelectionKeyName($q)
    {
        return $q->getModel()->getKeyName();
    }

}
