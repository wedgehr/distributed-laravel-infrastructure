<?php

namespace DistributedLaravel\Infrastructure\Http;

use Illuminate\Routing\Router;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Optimus\Api\System\RouteServiceProvider as ServiceProvider;
use DistributedLaravel\Infrastructure\Http\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

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

		parent::boot();

		$this->app->extend(ResponseFactoryContract::class, function () {
			return new ResponseFactory($this->app[ViewFactoryContract::class], $this->app['redirect']);
		});
	}
}
