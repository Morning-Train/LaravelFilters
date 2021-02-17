<?php

namespace MorningTrain\Laravel\Filters\Filters;

class WithFilter extends Filter
{

    public function __construct($relations, $callback = null)
    {
        parent::__construct(null);

        $this->when([], function($q) use($relations, $callback) {
            $q->with($relations, $callback);
        });
    }

}

