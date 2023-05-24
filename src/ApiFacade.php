<?php

namespace Origami\Api;

use Illuminate\Support\Facades\Facade;

class ApiFacade extends Facade
{
    /**
     * Get the registered component.
     *
     * @return object
     */
    protected static function getFacadeAccessor()
    {
        return 'origami.api';
    }
}
