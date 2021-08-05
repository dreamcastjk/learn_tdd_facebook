<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class ReverseScope implements Scope
{
    /**
     * @param Builder $builder
     * @param Model $model
     *
     * @return Builder
     */
    public function apply(Builder $builder, Model $model): Builder
    {
        return $builder->orderBy('id', 'desc');
    }
}
