<?php

namespace DistributedLaravel\Infrastructure\Database\Migrations;

use Illuminate\Database\Console\Migrations\MigrateCommand as IlluminateCommand;

/**
 * We are extending MigrateCommand to enforce stepping
 */
class MigrateCommand extends IlluminateCommand
{
	public function handle()
	{
		$this->input->setOption('step', true);

		parent::handle();
	}
}
