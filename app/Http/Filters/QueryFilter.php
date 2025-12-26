<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QueryFilter
{
    protected Builder $builder;
    protected Request $request;

    public function __construct(Builder $builder, Request $request)
    {
        $this->builder = $builder;
        $this->request = $request;
    }

    public function apply()
    {
        if ($this->request->has('search')) {
            $this->search($this->request->get('search'));
        }

        if ($this->request->has('role')) {
            $this->filterByRole($this->request->get('role'));
        }

        return $this->builder;
    }

    protected function search(string $keyword)
    {
        $this->builder->where(function ($q) use ($keyword) {
            $q->where('name', 'LIKE', "%{$keyword}%")
              ->orWhere('email', 'LIKE', "%{$keyword}%");
        });
    }

    protected function filterByRole(string $role)
    {
        $this->builder->where('role', $role);
    }
}
