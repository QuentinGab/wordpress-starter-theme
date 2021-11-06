<?php

// silence

use App\Models\Model;
use App\Models\QueryBuilder;
use App\Models\Wodel;

echo "<pre>";

dd(
    (new Model())->find(2)
);
echo "</pre>";
