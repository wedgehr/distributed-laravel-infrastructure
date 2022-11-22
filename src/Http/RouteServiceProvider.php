<?php

namespace DistributedLaravel\Infrastructure\Http;

use DistributedLaravel\Infrastructure\Http\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Routing\Router;
use Optimus\Api\System\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
	protected static string $idPattern = '[0-9]+';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot()
	{
		$router = $this->app->make(Router::class);

		$router->pattern('id', static::$idPattern);

		parent::boot($router);

		$this->app->extend(ResponseFactoryContract::class, function () {
			return new ResponseFactory($this->app[ViewFactoryContract::class], $this->app['redirect']);
		});
	}
}
