<?php namespace Origami\Api\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Config\Repository as Config;

class Stateless {

    /**
     * @var Config
     */
    private $config;

    /**
     * Create a new filter instance.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
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
        $this->config->set('session.driver', 'array');

        return $next($request);
    }

}
