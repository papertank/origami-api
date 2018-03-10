<?php

namespace Origami\Api;

use Exception;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Origami\Api\Pagination\PaginatorAdapter;

trait TransformResources {

    protected $transformer;
    protected $include_key = 'include';

	protected function buildPaginatedCollection($collection, $callback)
    {
        if ( ! method_exists($callback, 'transform') ) {
            throw new Exception('Unable to perform transformation on collection without callback');
        }

        $paginator = $collection;
        $collection = $paginator->getCollection();

        $resource = new Collection($collection, $callback);
        $resource->setPaginator(new PaginatorAdapter($paginator));

        $data = $this->getTransformer()->createData($resource)->toArray();

        return $data;
    }

    protected function buildCollection($collection, $callback)
    {
        if ( ! $collection OR empty($collection) ) {
            return [];
        }

        if ( ! method_exists($callback, 'transform') ) {
            throw new Exception('Unable to perform transformation on '.get_class($collection[0]).' without callback');
        }

        $resource = new Collection($collection, $callback);

        $data = $this->getTransformer()->createData($resource)->toArray();

        return $data;
    }

    protected function buildItem($item, $callback)
    {
        if ( ! method_exists($callback, 'transform') ) {
            throw new Exception('Unable to perform transformation on '.get_class($item).' without callback');
        }

        $resource = new Item($item, $callback);

        $data = $this->getTransformer()->createData($resource)->toArray();

        return $data;
    }

    protected function getTransformer()
    {
        if ( ! $this->transformer ) {
            $this->transformer = new Manager();
            $this->transformer->parseIncludes(
            	$this->getRequestIncludes()
            );
        }

        return $this->transformer;
    }

    public function getRequestIncludes()
    {
    	return app('request')->input($this->getIncludeKey(),'');
    }

    /**
     * @return string
     */
    public function getIncludeKey()
    {
        return $this->include_key;
    }

    /**
     * @param string $include_key
     */
    public function setIncludeKey($include_key)
    {
        $this->include_key = $include_key;
    }

}
