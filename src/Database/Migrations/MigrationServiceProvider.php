<?php

namespace DistributedLaravel\Infrastructure\Database\Migrations;

use Illuminate\Support\ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->app->bind(MigrateCommand::class, function ($app) {
			return new MigrateCommand($app['migrator'], $app['events']);
		});
	}
}
