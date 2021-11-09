<?php

namespace App\Models;

use QuentinGab\WordpressOrm\Wodel;

class Post extends Wodel
{
    protected string $post_type = 'post';
}
