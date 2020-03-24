<?php

namespace Recoded\WpQuery\Database\Relations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Recoded\WpQuery\Database\Eloquent\Model;

class BelongsToMany extends BelongsTo
{
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
        $keys = array_map(function (Model $model) {
            if (!$keys = data_get($model->getAttributes(), $this->foreignKey)) {
                return [];
            }

            return Arr::wrap($keys);
        }, $models);

        $this->query->whereKey(
            Arr::flatten($keys, 1),
        );
    }

    /**
     * @inheritDoc
     */
    public function getResults()
    {
        return $this->query->get();
    }

    /**
     * @inheritDoc
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, new Collection());
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
            if (!$keys = data_get($model->getAttributes(), $this->foreignKey)) {
                continue;
            }

            $model->setRelation($relation, new Collection(
                array_filter(array_map(fn ($key): ?Model => $results->get($key), array_filter(Arr::wrap($keys)))),
            ));
        }

        return $models;
    }
}
