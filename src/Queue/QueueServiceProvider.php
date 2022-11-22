<?php

namespace DistributedLaravel\Infrastructure\Queue;

use DistributedLaravel\Infrastructure\Queue\Console\WorkCommand;
use Illuminate\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->app->bind(WorkCommand::class, function ($app) {
			return new WorkCommand($app['queue.worker'], $app['cache.store']);
		});
	}
}
