<?php

namespace MorningTrain\Laravel\Filters\Filters;

class AlwaysFilter extends Filter
{

    public function __construct(\Closure $closure = null)
    {
        parent::__construct(null);

        $this->always($closure);
    }

}

