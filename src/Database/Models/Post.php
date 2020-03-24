<?php

namespace Recoded\WpQuery\Database\Models;

use Recoded\WpQuery\Database\Relations\BelongsTo;
use Recoded\WpQuery\Database\Relations\BelongsToMany;
use Recoded\WpQuery\Database\Relations\LinksMany;
use Recoded\WpQuery\Database\Relations\LinksOne;

class Post extends PostType
{
    public function author(): LinksOne
    {
        return $this->linksOne(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function replies(): LinksMany
    {
        return $this->linksMany(Comment::class)
            ->where('parent', 0);
    }
}
