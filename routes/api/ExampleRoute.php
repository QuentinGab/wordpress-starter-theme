<?php

use App\Controllers\ExampleController;

class ExampleRoute extends WP_REST_Controller
{

    public $namespace = "namespace";
    public $version = "v1";
    public $base = "base";

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes()
    {

        register_rest_route("{$this->namespace}/{$this->version}", '/' . $this->base, array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_items'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
                'args' => array(),
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_item'),
                'permission_callback' => array($this, 'create_item_permissions_check'),
                'args' => $this->get_endpoint_args_for_item_schema(true),
            ),
        ));
        register_rest_route("{$this->namespace}/{$this->version}", "/{$this->base}/(?P<id>\d+)", array(
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array($this, 'delete_item'),
                'permission_callback' => array($this, 'delete_item_permissions_check'),
                'args' => array(
                    'force' => array(
                        'default' => false,
                    ),
                ),
            ),
        ));
    }

    /**
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_items($request)
    {
        $controller = new ExampleController();

        return $controller->index($request);
    }


    /**
     * Create one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public function create_item($request)
    {
        $controller = new ExampleController();

        return $controller->store($request);
    }

    /**
     * Delete one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public function delete_item($request)
    {
        $params = $request->get_params();
        $id = $params['id'];

        $controller = new ExampleController();

        return $controller->destroy($request, $id);
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request)
    {
        //return true; <--use to make readable by all
        return is_user_logged_in() and current_user_can('administrator');
    }

    /**
     * Check if a given request has access to get a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_item_permissions_check($request)
    {
        $params = $request->get_params();
        $id = $params['id'];

        if ($id == get_current_user_id()) {
            return true;
        }

        return $this->get_items_permissions_check($request);
    }

    /**
     * Check if a given request has access to create items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function create_item_permissions_check($request)
    {
        return true;
    }

    /**
     * Check if a given request has access to update a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function update_item_permissions_check($request)
    {
        return current_user_can('administrator');
    }

    /**
     * Check if a given request has access to delete a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function delete_item_permissions_check($request)
    {
        return current_user_can('administrator');
    }

    /**
     * Get the query params for collections
     *
     * @return array
     */
    public function get_collection_params()
    {
        return array(
            'map' => array(
                'description' => 'Current page of the collection.',
                'type' => 'integer',
                'default' => 1,
                'sanitize_callback' => 'absint',
            ),
            'source' => array(
                'description' => 'Maximum number of items to be returned in result set.',
                'type' => 'integer',
                'default' => 10,
                'sanitize_callback' => 'absint',
            ),
            'setting' => array(
                'description' => 'Limit results to those matching a string.',
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }
}

// Function to register our new routes from the controller.
function prefix_register_example_routes()
{
    $controller = new ExampleRoute();
    $controller->register_routes();
}

add_action('rest_api_init', 'prefix_register_example_routes');
