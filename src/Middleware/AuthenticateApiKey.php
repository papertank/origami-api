<?php

namespace Origami\Api\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Origami\Api\Api;

class AuthenticateApiKey {
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
		$key = $request->header('x-api-key');

        if ( ! $this->api->auth($key) ) {
            return $this->api->response()->errorUnauthorized('Invalid key');
        }

		return $next($request);
	}

}
