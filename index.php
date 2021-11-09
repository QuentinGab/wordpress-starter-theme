<?php

// silence

use App\Models\Model;
use App\Models\QueryBuilder;
use App\Models\Wodel;

echo "<pre>";

$post = new Wodel([
    "post_title" => "Hello",
    'acf' => [
        'test_date' => "11/01/2021"
    ]
], true);
$post->save();
dd(
    $post,
);
echo "</pre>";
