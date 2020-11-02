<?php

namespace MorningTrain\Laravel\Filters\Filters;

use Carbon\Carbon;

class PeriodFilter extends Filter
{

    public function __construct($key, $scope)
    {

        $this->when($key, function ($q, $value) use($scope) {

            $from = data_get($value, 'from');
            $to = data_get($value, 'to');

            if ($from && $to) {

                if($this->cast_to_carbon === true) {
                    if(is_string($from)) {
                        $from = Carbon::createFromFormat($this->input_format, $from);
                    }

                    if(is_string($to)) {
                        $to = Carbon::createFromFormat($this->input_format, $to);
                    }
                }

                $q->{$scope}($from, $to);
            }

        });

        $this->default('from', Carbon::today()->format($this->input_format));
        $this->default('to', Carbon::today()->format($this->input_format));

    }

    /////////////////////////////////
    /// Parsing / casting
    /////////////////////////////////

    protected $input_format = 'Y-m-d';

    public function inputFormat($format)
    {
        $this->input_format = $format;

        return $this;
    }

    protected $cast_to_carbon = true;

    public function castToCarbon($should_cast = true)
    {
        $this->cast_to_carbon = $should_cast;

        return $this;
    }

    /////////////////////////////////
    /// Exporting
    /////////////////////////////////

    protected function getExportType()
    {
        return 'Period';
    }

}

