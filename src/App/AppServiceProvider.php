<?php

namespace DistributedLaravel\Infrastructure\App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use DistributedLaravel\Infrastructure\Version;
use DistributedLaravel\Infrastructure\Exceptions\Throwables\AppException;

class AppServiceProvider extends ServiceProvider
{
	protected function ensureApplicationIsNotRunningInDebugMode(): void
	{
		// sanity check that we aren't boot app in production debug mode
		if (config('app.debug') && app()->environment() === 'production') {
			Log::emergency('will not boot app in production with debug mode enabled');

			throw new \Exception('will not boot app in production with debug mode enabled');
		}
	}

	protected function extendValidators(): void
	{
		//
		// uuid validator
		//
		Validator::extend('uuid', function ($attribute, $value, $parameters, $validator) {
			$matches = preg_match(
				'/(?:[0-9]+)|(?:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/',
				$value
			);

			return (bool) $matches;
		});

		//
		// small_int validator
		//
		Validator::extend('small_int', function ($attribute, $value, $parameters, $validator) {
			return (bool) ($value >= -32767 && $value <= 32767);
		});

		/**
		 * Spaces Validator
		 */
		Validator::extend('not_whitespace', function ($attribute, $value, $parameters, $validator) {
			return ! ctype_space($value);
		});
	}

	protected function bootHashIds(): void
	{
		//
		// Hashids
		//
		$this->app->singleton(\Hashids\Hashids::class, function ($app) {
			$salt = $app['config']['app.hashids.salt'];

			if (empty($salt)) {
				throw AppException::config('hashids salt not provided');
			}

			return new \Hashids\Hashids($salt);
		});
		$this->app->alias(\Hashids\Hashids::class, 'Hashids');
		$this->app->alias('Hashids', 'hashids');
	}

	protected function bootShortUuid(): void
	{
		//
		// ShortUUID
		//
		$this->app->singleton(\PascalDeVink\ShortUuid\ShortUuid::class, function () {
			return new \PascalDeVink\ShortUuid\ShortUuid();
		});

		$this->app->alias(\PascalDeVink\ShortUuid\ShortUuid::class, 'ShortUUID');
		$this->app->alias('ShortUUID', 'shortuuid');
	}

	protected function initializeStats(): void
	{
		//
		// Stats
		//
		$statsInstance = new \DistributedLaravel\Infrastructure\App\Stats();
		$this->app->instance(\DistributedLaravel\Infrastructure\App\Stats::class, $statsInstance);
		$this->app->alias(\DistributedLaravel\Infrastructure\App\Stats::class, 'Stats');
	}

	protected function logQueryEvents(): void
	{
		if (! $this->app['config']['app.debug_queries']) {
			return;
		}

		DB::listen(function ($query) {
			Log::debug('Executed SQL', [
				'sql' => $query->sql,
				'bindings' => $query->bindings,
				'time' => $query->time,
				'queryCount' => app()->make('Stats')->getQueryCount(),
				// 'connection' => $query->connection->name,
			]);
		});
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	protected function boot()
	{
		$this->ensureApplicationIsNotRunningInDebugMode();
		$this->extendValidators();
		$this->bootHashIds();
		$this->bootShortUuid();
		$this->initializeStats();
		$this->logQueryEvents();

		// app is usually deployed behind a load balancer doing tls term,
		// leaving app unsure if https is to be used. it _always_ is,
		// force turn turn on https
		$this->app['request']->server->set('HTTPS', 'on');

		// detect versions if not cached
		if (empty(config('app.version'))) {
			Version::detectApiVersion();
		}
	}
}
