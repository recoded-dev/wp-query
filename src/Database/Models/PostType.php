<?php

namespace Recoded\WpQuery\Database\Models;

use Illuminate\Support\Str;
use Recoded\WpQuery\Database\Eloquent\Builder;
use Recoded\WpQuery\Database\Eloquent\Model;
use Recoded\WpQuery\Database\Query\Builder as QueryBuilder;

abstract class PostType extends Model
{
    protected string $namespace = 'wp';
    protected string $version = 'v2';
    protected ?string $restBase = null;

    public function extractLinksOneId(string $url): ?int
    {
        $namespace = preg_quote($this->getNamespace(), '/');
        $version = preg_quote($this->getVersion(), '/');
        $restBase = preg_quote($this->getRestBase(), '/');

        if (preg_match("/wp-json\/$namespace\/$version\/$restBase\/(?P<id>\d+)/", $url, $matches)) {
            return (int) $matches['id'];
        }

        return null;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getRestBase(): string
    {
        return $this->restBase ?: Str::of(class_basename($this))
            ->snake()
            ->plural();
    }

    /**
     * @inheritDoc
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function linksManyQueryKey(string $relation): string
    {
        return 'post';
    }

    /**
     * @return QueryBuilder|\Illuminate\Database\Query\Builder
     */
    public function newBaseQueryBuilder()
    {
        return $this->getConnection()
            ->query()
            ->repository(
                $this->getNamespace(),
                $this->getVersion(),
            )->postType($this->getRestBase());
    }

    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    public function newFromBuilder($attributes = [], $connection = null): self
    {
        $model = $this->newInstance([], true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);

        return $model;
    }

    public function newQuery()
    {
        return $this->newEloquentBuilder(
            $this->newBaseQueryBuilder(),
        )->setModel($this);
    }

    public function newRouteBindingQuery($value, $field = null)
    {
        return $this->newQuery()->where(
            $field ?: $this->getRouteKeyName(),
            $value,
        );
    }

    /**
     * @inheritDoc
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->newRouteBindingQuery($value, $field)
            ->first();
    }
}
