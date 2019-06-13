<?php

namespace MorningTrain\Laravel\Filters\Filters;

class Order extends Filter
{

    /**
     * @var array
     */
    protected $columns = [];
    protected $scopes = [];

    /**
     * @param string|array $columns
     * @return $this
     */
    public function scopes($scopes)
    {
        $this->scopes = array_merge($this->scopes, (array)$scopes);
        return $this;
    }

    /**
     * @param string|array $columns
     * @return $this
     */
    public function only($columns)
    {
        $this->columns = array_merge($this->columns, (array)$columns);
        return $this;
    }

    protected function validateColumn($column)
    {
        return empty($this->columns) || in_array($column, $this->columns);
    }

    protected function validateScope($scope)
    {
        return !empty($this->scopes) && in_array($scope, $this->scopes);
    }

    protected function getOrdersFromQueryInput($input)
    {

        // Scope

        if (is_string($input) && $this->validateScope($input)) {
            return [
                [
                    'scope' => $input,
                    'direction' => 'asc'
                ]
            ];
        }

        // Single column
        if (is_string($input) && $this->validateColumn($input)) {
            return [
                [
                    'column' => $input,
                    'direction' => 'asc'
                ]
            ];
        }

        // Multiple columns
        if (is_array($input)) {
            $orders = [];

            foreach ($input as $column => $direction) {
                if($this->validateScope($column)){
                    $orders[] = [
                        'scope' => $column,
                        'direction' => in_array(strtolower($direction), ['asc', 'desc']) ?
                            $direction :
                            'asc'
                    ];
                } else if ($this->validateColumn($column)) {
                    $orders[] = [
                        'column' => $column,
                        'direction' => in_array(strtolower($direction), ['asc', 'desc']) ?
                            $direction :
                            'asc'
                    ];
                }
            }

            if (!empty($orders)) {
                return $orders;
            }
        }
    }

    public function __construct()
    {
        $this->when('$order', function ($query, $input) {
            $orders = $this->getOrdersFromQueryInput($input);

            if (is_array($orders)) {
                $this->appliedOrders = $orders;
                foreach ($orders as $order) {
                    if(isset($order['scope'])){
                        $query->{$order['scope']}($order['direction']);
                    } else if(isset($order['column'])){
                        $query->orderBy($order['column'], $order['direction']);
                    }
                }
            }
        });
    }

    protected $appliedOrders;

    public function getMetadata()
    {
        if (is_array($this->appliedOrders)) {
            return [
                'order' => $this->appliedOrders
            ];
        }

        return [];
    }

    public function default($key, $value = null)
    {
        $this->default_values['$order'] = [$key => $value];

        return $this;
    }

}
