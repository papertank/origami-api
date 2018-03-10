<?php

namespace Origami\Api;

use Illuminate\Http\Request;

class Api {

    /**
     * @var array
     */
    private $config;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    public function __construct(array $config, Request $request, Response $response)
    {
        $this->config = $config;
        $this->request = $request;
        $this->response = $response;
    }

    public function config($key, $fallback = null)
    {
        return array_get($this->config, $key, $fallback);
    }

    public function auth($key)
    {
        $keys = $this->config('keys', []);

        return in_array($key, $keys);
    }

    public function response($message = null, $code = 200, $headers = [])
    {
        if ( func_num_args() == 0 ) {
            return $this->response;
        }

        return $this->response->make($message, $code, $headers);
    }

}
