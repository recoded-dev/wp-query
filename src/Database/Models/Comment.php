<?php

namespace Recoded\WpQuery\Database\Models;

use Recoded\WpQuery\Database\Relations\LinksMany;

class Comment extends PostType
{
    public function children(): LinksMany
    {
        return $this->linksMany(static::class);
    }

    public function linksManyQueryKey(string $relation): string
    {
        return 'parent';
    }
}
