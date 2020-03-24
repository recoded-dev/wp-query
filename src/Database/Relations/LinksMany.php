<?php

namespace Recoded\WpQuery\Database\Relations;

use Illuminate\Database\Eloquent\Collection;
use Recoded\WpQuery\Database\Eloquent\Model;

class LinksMany extends LinksOne
{
    protected string $keyFinderMethod = 'extractLinksManyId';

    public function addConstraints()
    {
        if (!$attributes = $this->parent->getAttributes()) {
            return;
        }

        $this->query->where(
            $this->queryKey,
            $this->findKey($attributes),
        );
    }

    /**
     * @inheritDoc
     */
    public function addEagerConstraints(array $models)
    {
        $keys = array_map(
            fn (Model $model) => $this->findKey($model->getAttributes()),
            $models,
        );

        $this->query->where($this->queryKey, $keys);
    }

    /**
     * @inheritDoc
     */
    public function getResults()
    {
        return $this->query->get();
    }

    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, new Collection());
        }

        return $models;
    }

    public function match(array $models, Collection $results, $relation)
    {
        $results = $results->groupBy($this->queryKey);

        foreach ($models as $model) {
            $key = $this->findKey($model->getAttributes());

            if ($key) {
                $model->setRelation($relation, $results->get($key, new Collection()));
            }
        }

        return $models;
    }
}
