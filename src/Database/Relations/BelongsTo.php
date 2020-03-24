<?php

namespace Recoded\WpQuery\Database\Relations;

use Illuminate\Database\Eloquent\Collection;
use Recoded\WpQuery\Database\Eloquent\Builder;
use Recoded\WpQuery\Database\Eloquent\Model;

class BelongsTo extends Relation
{
    protected string $foreignKey;

    public function __construct(Builder $query, Model $parent, string $relationName, string $foreignKey)
    {
        $this->foreignKey = $foreignKey;

        parent::__construct($query, $parent, $relationName);
    }

    /**
     * @inheritDoc
     */
    public function addConstraints()
    {
        if (!$attributes = $this->parent->getAttributes()) {
            return;
        }

        $this->query->whereKey(
            data_get($attributes, $this->foreignKey),
        );
    }

    /**
     * @inheritDoc
     */
    public function addEagerConstraints(array $models)
    {
        $keys = array_map(fn (Model $model) => data_get($model->getAttributes(), $this->foreignKey), $models);

        $this->query->whereKey($keys);
    }

    /**
     * @inheritDoc
     */
    public function getResults()
    {
        return $this->query->first();
    }

    /**
     * @inheritDoc
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }

        return $models;
    }

    /**
     * @inheritDoc
     */
    public function match(array $models, Collection $results, $relation)
    {
        $keyName = $this->related->getKeyName();
        $results = $results->keyBy($keyName);

        foreach ($models as $model) {
            if (!$key = data_get($model->getAttributes(), $this->foreignKey)) {
                continue;
            }

            $model->setRelation($relation, $results->get($key));
        }

        return $models;
    }
}
