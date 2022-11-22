<?php

namespace DistributedLaravel\Infrastructure\Queue\Console;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Console\WorkCommand as ConsoleWorkCommand;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Log;

/**
 * We are extending WorkCommand to redirect stdout
 * to our logging pipeline
 */
class WorkCommand extends ConsoleWorkCommand
{
	public function handle()
	{
		config(['app.daemon' => 'queue']);

		$this->followJobId();

		parent::handle();
	}

	protected function writeStatus(Job $job, $status, $type)
	{
		$level = $type === 'error' ? 'error' : 'info';

		$id = $job->getJobId();
		$name = $job->resolveName();

		Log::{$level}(sprintf('%s: %s', $status, $name), [
			'jobId' => $id,
			'name' => $name,
			'status' => strtolower($status),
			'type' => $type,
		]);
	}

	protected function followJobId()
	{
		$this->laravel['events']->listen(JobProcessing::class, function ($event) {
			config(['xtra.queue.job_id' => $event->job->getJobId()]);
		});
	}
}
