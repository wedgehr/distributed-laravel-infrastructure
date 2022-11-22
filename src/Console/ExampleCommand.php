<?php

namespace DistributedLaravel\Infrastructure\Console;

class ExampleCommand extends Command
{
	protected $signature = 'example-command';

	public function handle()
	{
		$this->info('hello world!');
	}
}
