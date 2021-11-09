<?php

namespace App\Models;

use QuentinGab\WordpressOrm\Wodel;

class Page extends Wodel
{
    protected string $post_type = 'page';
}
