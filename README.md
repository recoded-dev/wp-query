# WP Query
wp-query is a simple way to query data from Wordpress in Laravel

You can display a post very simply like this:
```php
<?php

use Illuminate\Support\Facades\Route;
use Recoded\WpQuery\Database\Models\Post;

Route::get('post/{post}', function (Post $post) {
    return $post;
});
```

The default Post model has the following relations which you can query (and eagerload):
- author (User)
- categories (Category)
- featuredMedia (Media)
- replies (Comment)

The default Comment model also has a children relationship. This contains all comments that have the current comment as parent.
