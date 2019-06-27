<?php

namespace MorningTrain\Laravel\Filters\Filters;

class EnumFilter extends Filter
{

    protected $enum_slug = null;
    protected $enum_class = null;

    public function __construct($enum, $constraint)
    {

        $this->from($enum);

        $this->when($constraint, function ($q, $values) use ($constraint) {

            if (!is_array($values)) {
                $values = [$values];
            }

            $q->whereIn($constraint, $values);
        });

    }

    /////////////////////////////////
    /// Enum specifics
    /////////////////////////////////

    protected $enumOptions = [];

    public function options(array $options)
    {
        $this->enumOptions = $options;
        return $this;
    }

    public function from($enum)
    {
        $this->enum_class = $enum;
        $this->enum_slug = $enum::basename();
        $this->enumOptions = $enum::values();
        return $this;
    }

    /////////////////////////////////
    /// Exporting
    /////////////////////////////////

    protected function getExportType()
    {
        return 'Enum';
    }

    protected function extraExport()
    {
        return ['enum' => $this->enum_slug];
    }

}

