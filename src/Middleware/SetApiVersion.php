<?php

namespace Origami\Api\Middleware;

use Closure;
use Origami\Api\Api;

class SetApiVersion
{
    /**
     * @var Api
     */
    private $api;

    /**
     * Create a new filter instance.
     *
     * @param  Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $version = $this->api->getVersion($request->header('x-api-version'));

        if ($version) {
            $this->api->setCurrentVersion($version);
        }

        return $next($request);
    }
}
