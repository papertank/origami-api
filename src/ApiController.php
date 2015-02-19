<?php namespace Origami\Api;

use Illuminate\Routing\Controller;

class ApiController extends Controller {

    public function __construct()
    {
        $this->middleware('Origami\Api\Middleware\Stateless');
    }

    /**
     * @return \Origami\Api\Response
     */
    public function response($data = null)
    {
        if ( ! is_null($data) ) {
            return app('api')->response()->data($data);
        }

        return app('api')->response();
    }

}