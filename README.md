# Origami API Package

This package simplifies the process of setting up an API using Laravel 6. Many of the practices utilised in this package are courtesy of [Build APIs you Won't Hate](https://leanpub.com/build-apis-you-wont-hate) by Phil Sturgeon.

## Installation

Install this package through Composer.

`composer require origami/api`

### Requirements

This package is designed to work with Laravel >= 6 currently.

### Configuration

There are some API configuration options that you'll want to overwrite. First, publish the default configuration.

```bash
php artisan vendor:publish
```

This will add a new configuration file to: `config/api.php`.

```php
<?php
return array(

	'keys' => [
		env('API_KEY', 'secret')
	],

);
```

#### keys

This is the valid list of API keys that authenticate requests. By default we support an environment variable of `API_KEY` which you can set in your .env file.

## Middleware

This package includes two Middleware classes for Laravel 6

### Origami\Api\Middleware\AuthenticateApiKey

The **AuthenticateApiKey** Middleware is designed to guard Api routes against unauthorised access. We recommend you include it on all routes as follows, unless you have a public API.

```php
Route::group(['prefix' => 'api/v1', 'namespace' => 'Api', 'middleware' => 'Origami\Api\Middleware\AuthenticateApiKey'], function()
{
	Route::get('tasks/{id}', 'Tasks@show');
	Route::post('tasks/{id}', 'Tasks@update');
});
```

## Controllers

We provide a helpful **ApiController** base controller class that includes a `response` method, allowing you to return json responses or get access to the **Origami\Api\Response** class which offers a variety of helpers methods.


## Responses

The **Origami/Api/Response** class offers a variety of helper methods and ultimately uses the `Illuminate\Contracts\Routing\ResponseFactory` Laravel class to return a json response with appropriate headers.

You can use the API Response class in your controller by using the `response` helper method:

```php
	public function index()
	{
		$items = new Collection(['one','two','three']);

		// Calling with a single argument returns a json response
		return $this->response($items);
	}
```

or

```php
	public function index()
	{
		$items = new Collection(['one','two','three']);

		// Calling with no argument returns the response object
		return $this->response()->data($items);
	}

	public function find($id)
	{
		$item = Item::find($id);

		if ( ! $item ) {
			// Using the response object you can call helper methods.
			return $this->response()->errorNotFound();
		}

		return $this->response()->data($item);
	}
```

### Items or Collections

We make use of the excellent [league/fractal](https://github.com/thephpleague/fractal) PHP package to parse and transform Eloquent models and collections. For more information visit [fractal.thephpleague.com](http://fractal.thephpleague.com/)

There are three helper methods on the response object

* resourceItem
* resourceCollection
* resourcePaginator

## Full Example

Routes: app/Http/routes.php
```php
<?php
	Route::group(['prefix' => 'api/v1', 'namespace' => 'Api', 'middleware' => 'Origami\Api\Middleware\AuthenticateApiKey'], function()
{
    Route::get('items', 'Items@index');
    Route::get('tasks/{id}', 'Tasks@show');
});
```

Controller: app/Http/Controllers/Api/Items.php
```php
<?php namespace App\Http\Controllers\Api;

use Origami\Api\ApiController;
use App\Items\Item; // Assuming an eloquent Model;
use App\Items\ItemTransformer;

class Items extends ApiController {

	public function index()
	{
		$items = Item::paginate(15);

		return $this->response()->resourcePaginator($items, new ItemTransformer);
	}

	public function show($id)
	{
		$item = Item::find($id);

		if ( ! $item ) {
			return $this->response()->errorNotFound('Item not found');
		}

		return $this->response()->resourceItem($item, new ItemTransformer);
	}

}
```

Transformer: app/Items/ItemTransformer.php
```php
<?php namespace App\Items;

use Origami\Api\Transformer;

class ItemTransformer extends Transformer {

    public function transform(Item $item)
    {
        return [
            'id' => $item->id,
            'title' => $task->title,
            'created_at' => $task->created_at->toDateTimeString()
        ];
    }

}
```


## License

[View the license](http://github.com/papertank/origami-api/blob/master/LICENSE)
