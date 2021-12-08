<?php

// silence

use App\Models\Page;
use App\Models\Post;

$page = Page::init()->first();

echo "<pre>";

dd(
    $page
);
echo "</pre>";
