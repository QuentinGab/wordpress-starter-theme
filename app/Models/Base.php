<?php

namespace App\Models;

class Base
{
    protected ?QueryBuilder $queryBuilder = null;

    public ?int $limit = null;

    protected $driver = "mysql";

    protected $casts = [];

    /**
     * perform action before creating a new post
     */
    public function creating()
    {
        # code...
    }

    /**
     * perform action before updating the post
     */
    public function updating()
    {
        # code...
    }

    /**
     * perform action before deleting post
     */
    public function deleting()
    {
        # code...
    }

    protected function fill(array $data, $casts = false)
    {
        if ($casts) {
            array_walk($data, function (&$value, $key) {
                if (array_key_exists($key, $this->casts)) {
                    if (is_callable($this->casts[$key])) {
                        $value = $this->casts[$key]($value);
                    } elseif (
                        $this->casts[$key] === "int"
                    ) {
                        $value = intval($value);
                    }
                }
            });
        }

        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }

    public function where($column, $operator, $value = null)
    {
        $this->initQueryBuilder();

        if ($value) {
            $this->queryBuilder->where($column, $operator, $value);
        } else {
            $this->queryBuilder->where($column, "=", $operator);
        }

        return $this;
    }

    public function orWhere($column, $operator, $value = null)
    {
        $this->initQueryBuilder();

        if ($value) {
            $this->queryBuilder->orWhere($column, $operator, $value);
        } else {
            $this->queryBuilder->orWhere($column, "=", $operator);
        }

        return $this;
    }

    public function order($orderBy, $order = "DESC")
    {
        $this->initQueryBuilder();

        $this->queryBuilder->order($orderBy, $order);

        return $this;
    }

    public function limit($value)
    {
        $this->initQueryBuilder();

        $this->queryBuilder->limit($value);

        return $this;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }
}
