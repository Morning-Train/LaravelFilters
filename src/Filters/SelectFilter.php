<?php

namespace MorningTrain\Laravel\Filters\Filters;

use Closure;
use Illuminate\Database\Eloquent\Model;

class SelectFilter extends Filter
{

    protected $select_options = [];

    public function __construct($constraint)
    {

        $this->when($constraint, function ($q, $values) use ($constraint) {

            if (!is_array($values)) {
                $values = [$values];
            }

            $this->applySelectionToQuery($q, $constraint, $values);
        });

    }

    /////////////////////////////////
    /// Dropdown specifics
    /////////////////////////////////

    protected function applySelectionToQuery($q, $constraint, $values)
    {
        $q->whereIn($constraint, $values);
    }

    public function options($options)
    {
        if (is_array($options) || $options instanceof Closure) {
            $this->select_options = $options;
        }

        return $this;
    }

    public function getOptions(): array
    {
        return ($this->select_options instanceof Closure) ?
            ($this->select_options)() :
            $this->select_options;
    }

    public function source($source, $title = null, $key = 'id') {

        if($source instanceof \Illuminate\Database\Eloquent\Builder || $source instanceof \Illuminate\Database\Query\Builder) {

            $this->options(function () use ($source, $title, $key) {
                $options = $source->get()->pluck($title, $key);

                $options->transform(function($item) {

                    if($item instanceof Model) {
                        foreach($item->getAttributes() as $attribute) {
                            if(is_string($attribute)) {
                                return $attribute;
                            }
                        }
                        foreach($item->getAttributes() as $attribute) {
                            if(is_numeric($attribute)) {
                                return $attribute;
                            }
                        }
                        return $attribute;
                    }

                    return $item;
                });

                return $options->toArray();
            });
        }

        return $this;
    }

    /////////////////////////////////
    /// Exporting
    /////////////////////////////////

    protected function getExportType()
    {
        return 'Select';
    }

    protected function extraExport()
    {
        return ['options' => $this->getOptions()];
    }

}

