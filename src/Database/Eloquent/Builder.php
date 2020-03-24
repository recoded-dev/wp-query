<?php

namespace Recoded\WpQuery\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Arr;

/**
 * @mixin \Recoded\WpQuery\Database\Query\Builder
 */
class Builder extends EloquentBuilder
{
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->query->where(...func_get_args());

        return $this;
    }

    public function whereKey($key, bool $not = false): self
    {
        $key = array_map(
            fn ($i) => $i instanceof Model ? $i->getKey() : $i,
            Arr::wrap($key),
        );

        $this->query->where(
            $not ? 'exclude' : 'include',
            $key,
        );

        return $this;
    }

    public function whereKeyNot($key): self
    {
        return $this->whereKey($key, true);
    }

    public function whereSlug(string $slug = null): self
    {
        $this->query->where('slug', $slug);

        return $this;
    }
}
