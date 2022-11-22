<?php

namespace DistributedLaravel\Infrastructure\Logging;

use Illuminate\Support\Arr;
use Cumulati\Monolog\LogContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
	protected array $configMappers = [];

	/**
	 * push a config mapper
	 *
	 * @param callable $cb The callback receives (array $record, array $xtra),
	 *    and should return the modified $record.
	 */
	public function pushConfigMapper(callable $cb): void
	{
		$this->configMappers[] = $cb;
	}

	public function boot(): void
	{
		$monolog = Log::getLogger();
		LogContext::setDefaultAppendCtxId(true);
		LogContext::setDefaultLogger($monolog);
		LogContext::setDefaultKeyCounter('_c');
		LogContext::setDefaultKeyTimer('_t');

		$monolog->pushProcessor(function ($record) {
			$xtra = config('xtra');

			// Request
			if ($rid = Arr::get($xtra, 'request.id')) {
				$record['extra']['request'] = $rid;
			}

			// Transaction
			if ($tid = Arr::get($xtra, 'transaction.id')) {
				$record['extra']['transaction'] = $tid;
			}

			// User
			if ($uid = Arr::get($xtra, 'user.id')) {
				$record['extra']['user'] = $uid;
			}

			// workerd / queue job id
			if ($jobId = Arr::get($xtra, 'queue.job_id')) {
				$record['extra']['workerd_job_id'] = $jobId;
			}

			// container hostname
			if ($hostname = Arr::get($xtra, 'container.hostname')) {
				$record['extra']['container'] = $hostname;
			}

			// octane worker
			if ($owid = Arr::get($xtra, 'octane.worker.id')) {
				$record['extra']['octane_worker'] = $owid;
			}

			// microservice name
			if ($msn = Arr::get($xtra, 'microservice.name')) {
				$record['extra']['microservice'] = $msn;
			}

			// daemon
			$daemon = config('app.daemon');
			if ($daemon) {
				$record['extra']['daemon'] = $daemon;
			}

			if ($ip = Arr::get($xtra, 'client_ip')) {
				$record['extra']['ip'] = $ip;
			}

			$v = config('app.version');
			if ($v) {
				$record['extra']['v'] = $v;
			}

			foreach ($this->configMappers as $map) {
				$record = $map($record, $xtra);
			}

			return $record;
		});
	}
}
