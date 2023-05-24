<?php

namespace Origami\Api;

use Illuminate\Routing\Controller;

class ApiController extends Controller {

    /**
     * @return \Origami\Api\Response
     */
    public function response($data = null)
    {
        if ( ! is_null($data) ) {
            return app('origami.api')->response()->data($data);
        }

        return app('origami.api')->response();
    }

}
