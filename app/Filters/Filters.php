<?php

namespace App\Filters;
use Illuminate\Http\Request;

abstract class Filters
{
    protected $request;
    protected $builder;
    protected $filters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }
        return $this->builder;
    }

    /**
     * 获取需要过滤的值
     *
     * @return array
     */
    protected function getFilters()
    {
        // return array_filter($this->request->intersect($this->filters));
        return array_filter($this->request->only($this->filters));
    }
}