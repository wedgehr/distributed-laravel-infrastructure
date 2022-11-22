<?php

namespace DistributedLaravel\Infrastructure\Queue\Console;

use DistributedLaravel\Infrastructure\Console\Command;
use DistributedLaravel\Infrastructure\Queue\Tasks\TestTask;

class QueueTestCommand extends Command
{
	protected $signature = 'queue:dispatch-test {--count=1}';

	public function handle()
	{
		$count = (int) $this->option('count');

		for ($i = 0; $i < $count; $i++) {
			TestTask::dispatch();
		}

		$this->infof('Dispatched %d TestTask instances.', $count);
	}
}
