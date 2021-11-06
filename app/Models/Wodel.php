<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Collection;

/**
 * This is a Wordpress Model
 * you can use it to 
 */
class Wodel extends Base
{
    /**
     * post, page or custom post type
     */
    protected string $post_type = 'page';

    public array $order = [
        'orderby' => 'date',
        'order' => 'DESC'
    ];

    public ?int $limit = -1;

    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    public function get($limit = null)
    {
        $this->initQueryBuilder();

        if ($limit) {
            $this->limit($limit);
        }

        $posts = get_posts(
            $this->queryBuilder->buildWpQuery()
        );

        $posts = array_map(
            function ($item) {
                return new static(
                    get_object_vars(
                        $item
                    )
                );
            },
            $posts
        );

        return new Collection($posts);
    }

    public function all()
    {
        return $this->get(-1);
    }

    public function find($id)
    {
        $this->where('p', $id);

        return $this->first();
    }

    public function first()
    {
        $posts = $this->get(1);

        return $posts->first();
    }

    public function save()
    {
        $isNewPost = !!$this->ID;

        if ($isNewPost) {
            $this->creating();
        } else {
            $this->updating();
        }

        $result = wp_insert_post(
            [
                'ID' => $this->ID ?? 0,
                'post_content' => $this->post_content ?? '',
                'post_title' => $this->post_title ?? '',
                'post_excerpt' => $this->post_excerpt ?? '',
                'post_status' => $this->post_status ?? 'draft',
                'post_type' => $this->post_type ?? 'post',
                'comment_status' => $this->comment_status ?? '',
                'post_password' => $this->post_password ?? '',
                'post_parent' => $this->post_parent ?? 0
            ]
        );

        if (!$result) {
            throw new Exception("Post {$this->ID}:{$this->post_title} can't be saved");
        }

        if ($result) {
            $this->ID = $result;
        }

        return $this;
    }

    public function delete()
    {
        $this->deleting();
    }

    public function permalink()
    {
        return get_post_permalink($this->ID);
    }

    public function content()
    {
        return apply_filters('the_content', $this->post_content);
    }

    protected function initQueryBuilder()
    {
        if (!$this->queryBuilder) {
            $this->queryBuilder = new QueryBuilder();
            $this->queryBuilder->where('post_type', '=', $this->post_type);
        }
    }
}
