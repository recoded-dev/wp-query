<?php

namespace Recoded\WpQuery\Database\Concerns;

use function GuzzleHttp\Psr7\parse_query;
use Illuminate\Support\Str;
use Recoded\WpQuery\Database\Relations\BelongsTo;
use Recoded\WpQuery\Database\Relations\BelongsToMany;
use Recoded\WpQuery\Database\Relations\LinksMany;
use Recoded\WpQuery\Database\Relations\LinksOne;

/**
 * @mixin \Recoded\WpQuery\Database\Eloquent\Model
 */
trait HasRelationships
{
    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        $foreignKey ??= Str::snake($this->guessBelongsToRelation());

        $instance = $this->newRelatedInstance($related);

        return new BelongsTo($instance->newQuery(), $this, __FUNCTION__, $foreignKey);
    }

    public function belongsToMany(
        $related,
        $foreignKey = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $relation = null
    ) {
        $foreignKey ??= Str::snake($this->guessBelongsToRelation());

        $instance = $this->newRelatedInstance($related);

        return new BelongsToMany($instance->newQuery(), $this, __FUNCTION__, $foreignKey);
    }

    public function extractLinksManyId(string $url, string $key): ?int
    {
        $query = parse_query(parse_url($url, PHP_URL_QUERY));

        return isset($query[$key]) ? (int) $query[$key] : null;
    }

    protected function linksMany(string $related, string $data = null): LinksMany
    {
        $instance = $this->newRelatedInstance($related);

        return new LinksMany(
            $instance->newQuery(),
            $data ?: '_links.' . $this->guessBelongsToRelation() . '.0.href',
            $this->linksManyQueryKey(__FUNCTION__),
            __FUNCTION__,
            $this,
        );
    }

    protected function linksOne(string $related, string $data = null): LinksOne
    {
        $instance = $this->newRelatedInstance($related);

        return new LinksOne(
            $instance->newQuery(),
            $data ?: '_links.' . $this->guessBelongsToRelation() . '.0.href',
            $this->linksManyQueryKey(__FUNCTION__),
            __FUNCTION__,
            $this,
        );
    }

    abstract public function extractLinksOneId(string $url): ?int;

    abstract public function linksManyQueryKey(string $relation): string;
}
