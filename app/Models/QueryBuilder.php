<?php

namespace App\Models;

class QueryBuilder
{
    public string $driver = "mysql";

    public string $table = "";

    public array $where = [];

    public ?int $limit = null;

    public array $order = [
        'orderby' => 'id',
        'order' => 'ASC'
    ];


    public function __construct()
    {
        # code...
    }

    public function where($column, $operator = "=", $value)
    {
        $this->where[$column] = [
            'type' => 'AND',
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }

    public function orWhere($column, $operator = "=", $value)
    {
        $this->where[$column] = [
            'type' => 'OR',
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }

    public function buildWpQuery(): array
    {
        $defaults =
            [
                'post_status' => 'publish',
            ];

        $formattedWhere = array_map(
            function ($item) {
                return $item['value'];
            },
            $this->where
        );

        return array_merge(
            $defaults,
            $this->order,
            [
                'numberposts' => $this->limit
            ],
            $formattedWhere
        );
    }

    public function buildSqlQuery(): string
    {
        global $wpdb;

        $order = "ORDER BY {$this->order['orderby']} {$this->order['order']}";

        $from = "FROM {$this->table}";

        if ($this->driver === "mysql") {
            $limit = $this->limit ? "LIMIT {$this->limit}" : "";
        } else {
            $limit = $this->limit ? "TOP {$this->limit}" : "";
        }

        if (empty($this->where)) {
            if ($this->driver === "mysql") {
                return "SELECT * $from $order $limit";
            } else {
                return "SELECT $limit * $from $order";
            }
        }

        $where = array_values($this->where);

        $values = array_map(function ($item) {
            return $item['value'] ?? null;
        }, $where);

        $values_type = array_map(
            function ($item) {
                if (is_int($item)) {
                    return "%d";
                } elseif (is_float($item)) {
                    return "%f";
                }
                return "%s";
            },
            $values
        );

        $keys = array_keys($this->where);

        $length = count($keys);

        $whereAsSql = "";
        for ($i = 0; $i < $length; $i++) {
            if ($i > 0 and $i < $length) {
                $whereAsSql .= " {$where[$i]['type']} ";
            }

            $whereAsSql .= $keys[$i] . $where[$i]['operator'] . $values_type[$i];
        }

        if ($this->driver === "mysql") {
            return $wpdb->prepare(
                "SELECT * $from WHERE $whereAsSql $order $limit",
                $values
            );
        } else {
            return $wpdb->prepare(
                "SELECT $limit * $from WHERE $whereAsSql $order",
                $values
            );
        }
    }

    public function driver(string $value)
    {
        $this->driver = $value;
        return $this;
    }

    public function table(string $name): static
    {
        $this->table = $name;
        return $this;
    }

    public function limit(?int $value): static
    {
        $this->limit = $value;
        return $this;
    }

    public function order($orderBy, $order = "DESC"): static
    {
        if (is_array($orderBy)) {
            $orderBy = join(",", $orderBy);
        }

        $this->order = [
            'orderby' => $orderBy,
            'order' => $order
        ];

        return $this;
    }
}