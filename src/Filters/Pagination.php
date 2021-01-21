<?php

namespace MorningTrain\Laravel\Filters\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Pagination extends Filter
{

    const REQUIRED = true;

    /**
     * Limit number of entries (per page)
     *
     * @var int
     */
    protected $per_page = 10;

    /**
     * Stores current page
     *
     * @var int
     */
    protected $page = 1;

    /**
     * Stores number of pages
     *
     * @var int
     */
    protected $pages = 1;

    /**
     * Number of total results
     *
     * @var int
     */
    protected $count = 0;

    /**
     * @var bool
     */
    protected $paginated = false;

    /**
     * @param int $per_page
     * @return $this
     */
    public function shows(int $per_page = 10)
    {
        $this->per_page = $per_page;
        $this->default('$per_page', $this->per_page);
        return $this;
    }

    /**
     * @param int $page
     * @return $this
     */
    public function startsAt(int $page)
    {
        $this->page = min($page, $this->pages);
        $this->default('$page', $this->page);
        return $this;
    }

    public function setPages(int $count)
    {
        $this->pages = ceil($count / $this->per_page);
        return $this;
    }

    protected function getOffset()
    {
        return ($this->page - 1) * ($this->per_page);
    }

    protected function getLimit()
    {
        return $this->per_page;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getPerPage()
    {
        return $this->per_page;
    }

    public function __construct()
    {

        $this->default('$page', $this->page);
        $this->default('$per_page', $this->per_page);

        $this->when('$per_page', function (Builder $query, $per_page) {
            $this->shows(intval($per_page));
        });

        $this->when('$page', function (Builder $query, $page) {

            if ($this->getLimit() === 0) {
                return $query;
            }

            $pages = 1;
            $start_page = $page;
            if(is_array($page)) {
                $pages = count($page);
                $start_page = $page[0];
            }

            // store count
            $this->count = $query->count();

            // store total pages
            $this->setPages($this->count);

            // store page
            $this->startsAt(intval($start_page));

            // set applied filter
            $this->paginated = true;

            // limit
            $query->limit($this->getLimit() * $pages);

            // offset
            $query->offset($this->getOffset());
        });
    }

    public function paginated()
    {
        return $this->paginated;
    }

    public function export()
    {
        return array_merge(parent::export(), [
            'pagination' => [
                'page' => $this->page,
                'per_page' => $this->per_page,
                'pages' => $this->pages,
                'count' => $this->count
            ],
        ]);
    }

    public function getMetadata()
    {
        return $this->export();
    }

    public function apply(Builder $query, Request $request = null)
    {
        // Apply default providers
        foreach ($this->providers as $provider) {
            $args = $this->getArguments($provider['keys'], $request);

            if (is_array($args)) {
                $provider['apply'] ($provider['keys'], $query, ...$args);
            }
        }
    }

}
