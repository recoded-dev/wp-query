<?php

namespace Recoded\WpQuery\Database\Relations;

use Recoded\WpQuery\Database\Eloquent\Builder;
use Recoded\WpQuery\Database\Eloquent\Model;

abstract class Relation extends \Illuminate\Database\Eloquent\Relations\Relation
{
    protected string $relationName;

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(Builder $query, Model $parent, string $relationName)
    {
        $this->query = $query;
        $this->parent = $parent;
        $this->related = $query->getModel();
        $this->relationName = $relationName;

        $this->addConstraints();
    }
}
