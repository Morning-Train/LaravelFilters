<?php

namespace MorningTrain\Laravel\Filters\Filters;

class VoidFilter extends Filter {

    public function __construct($key)
    {
        parent::__construct(null);

        $this->when($key, function(){

        });
    }

}