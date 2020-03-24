<?php

namespace Recoded\WpQuery\Database\Relations;

use Illuminate\Database\Eloquent\Collection;
use Recoded\WpQuery\Database\Eloquent\Builder;
use Recoded\WpQuery\Database\Eloquent\Model;

class LinksOne extends Relation
{
    protected string $data;
    protected string $keyFinderMethod = 'extractLinksOneId';
    protected string $queryKey;

    public function __construct(Builder $query, string $data, string $queryKey, string $relationName, Model $parent)
    {
        $this->data = $data;
        $this->queryKey = $queryKey;

        parent::__construct($query, $parent, $relationName);
    }

    protected function findKey(array $data): ?int
    {
        return $this->related->{$this->keyFinderMethod}(
            $url = data_get($data, $this->data),
            $this->queryKey,
        );
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
        return $models;
    }

    /**
     * @inheritDoc
     */
    public function match(array $models, Collection $results, $relation)
    {
        $results = $results->keyBy('id');

        foreach ($models as $model) {
            $key = $this->findKey($model->getAttributes());

            if ($key) {
                $model->setRelation($relation, $results->get($key));
            }
        }

        return $models;
    }
}
