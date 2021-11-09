<?php

namespace App\Controllers;

use \WP_REST_Request;
use \WP_Error;
use \WP_REST_Response;

class ExampleController extends Controller
{
    public function __construct()
    {
        // 
    }

    /**
     * @param WP_REST_Request|null $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function index($request = null)
    {
        return new WP_REST_Response(['data' => null], 200);
    }

    /**
     * @param WP_REST_Request|null $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function store($request = null)
    {
        return new WP_REST_Response(['data' => null], 200);
    }

    /**
     * @param WP_REST_Request|null $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function update($request = null)
    {
        return new WP_REST_Response(['data' => null], 200);
    }

    /**
     * @param WP_REST_Request|null $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function destroy($request = null)
    {
        return new WP_REST_Response(['data' => null], 200);
    }
}
