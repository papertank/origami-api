<?php

namespace Origami\Api;

use Exception;
use Origami\Api\Version;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Origami\Api\VersionsCollection;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Api
{

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

    /**
     * @var Collection
     */
    private $versions;

    /**
     * @var Version|null
     */
    protected $currentVersion;

    /**
     * @var Version|null
     */
    protected $defaultVersion;

    public function __construct(array $config, Request $request, Response $response)
    {
        $this->config = $config;
        $this->request = $request;
        $this->response = $response;
    }

    public function config($key, $fallback = null)
    {
        return Arr::get($this->config, $key, $fallback);
    }

    public function auth($key)
    {
        $keys = $this->config('keys', []);

        return in_array($key, $keys);
    }

    public function renderException($request, Exception $e)
    {
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }

        if ($e instanceof ValidationException) {
            return $this->response()->errorValidation($e->validator->errors(), implode(', ', $e->validator->errors()->all('')));
        }

        if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
            return $this->response()->errorNotFound($e->getMessage() ? : 'Not Found');
        }

        if ($e instanceof AuthorizationException || $e instanceof AuthenticationException) {
            return $this->response()->errorForbidden($e->getMessage());
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->response()->errorMethod('Method not allowed');
        }

        if ($e instanceof MaintenanceModeException) {
            return $this->response()->error('We are temporarily offline for essential maintenance. Please check back shortly.', null, 503);
        }

        return $this->response()->error($e->getMessage());
    }

    public function response($message = null, $code = 200, $headers = [])
    {
        if (func_num_args() == 0) {
            return $this->response;
        }

        return $this->response->make($message, $code, $headers);
    }

    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }

    public function version()
    {
        return $this->getCurrentVersion();
    }

    public function getVersion($version)
    {
        return $this->versions()->get($version);
    }

    public function setCurrentVersion($version)
    {
        if ($version instanceof Version) {
            $this->currentVersion = $version;
            return $this;
        }

        $version = $this->versions()->get($version);

        if (!$version) {
            throw new Exception('Invalid api version');
        }

        $this->currentVersion = $version;

        return $this;
    }

    public function getDefaultVersion()
    {
        return $this->defaultVersion;
    }

    public function setDefaultVersion($version)
    {
        if ($version instanceof Version) {
            $this->defaultVersion = $version;
            return $this;
        }

        $version = $this->versions()->get($version);

        if (!$version) {
            throw new Exception('Invalid api version');
        }

        $this->defaultVersion = $version;

        return $this;
    }

    public function versions()
    {
        return $this->versions;
    }

    public function setVersions(array $versions)
    {
        $this->versions = VersionsCollection::make($versions)
                            ->values()
                            ->filter()
                            ->mapWithKeys(function ($date) {
                                return [$date => new Version($date)];
                            });
        
        return $this;
    }
    
    public function detectVersions()
    {
        $versions = $this->config('versions.all');
        $defaultVersion = $this->config('versions.default', date('Y-m-d'));

        if ($versions) {
            $this->setVersions($versions);
        }

        if ($defaultVersion) {
            $this->setDefaultVersion($defaultVersion);
            $this->setCurrentVersion($defaultVersion);
        }

        return $this;
    }
}
