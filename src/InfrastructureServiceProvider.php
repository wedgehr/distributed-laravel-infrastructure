<?php

namespace DistributedLaravel\Infrastructure;

use Illuminate\Support\ServiceProvider;
use DistributedLaravel\Infrastructure\Console\ExampleCommand;
use DistributedLaravel\Infrastructure\Config\ConfigCacheCommand;
use DistributedLaravel\Infrastructure\Queue\Console\WorkCommand;
use DistributedLaravel\Infrastructure\Queue\Console\QueueTestCommand;
use DistributedLaravel\Infrastructure\Database\Migrations\MigrateCommand;

class InfrastructureServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->commands([
			// misc
			 QueueTestCommand::class,
			 WorkCommand::class,

			 ConfigCacheCommand::class,
			 MigrateCommand::class,
			 ExampleCommand::class,
			 // FreshCommand::class,
		]);
	}
}
