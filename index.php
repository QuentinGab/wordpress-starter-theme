<?php

// silence

use App\Models\Page;
use App\Models\Post;

echo "<pre>";


dd(
    Post::init()->first(),
    Page::init()->first()
);
echo "</pre>";
