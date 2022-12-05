<?php

namespace DistributedLaravel\Infrastructure\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

abstract class Provider extends ServiceProvider implements DeferrableProvider
{
	protected string $resource;

	/**
	 * @var array<string, string>
	 */
	protected array $policies;

	public function register()
	{
		if (isset($this->resource)) {
			$this->registerResource();
			$this->registerPolicies();

			return;
		}

		Log::warning('This probably should not happen');
	}

	protected function registerResource()
	{
		$parts = explode('\\', $this->resource);

		$this->app->singleton($this->resource);
		$this->app->alias($this->resource, array_pop($parts));
	}

	protected function registerPolicies()
	{
		if (! isset($this->policies)) {
			return;
		}

		foreach ($this->policies as $key => $value) {
			Gate::policy($key, $value);
		}
	}

	public function provides()
	{
		if (! isset($this->resource)) {
			return [];
		}

		$parts = explode('\\', $this->resource);

		return [
			$this->resource,
			array_pop($parts),
		];
	}
}
