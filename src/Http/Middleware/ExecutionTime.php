<?php

namespace DistributedLaravel\Infrastructure\Http\Middleware;

use Closure;
use Facades\Stats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ExecutionTime
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		$rsp = $next($request);

		// check if a file is being downloaded to the client
		if ($rsp instanceof BinaryFileResponse) {
			Log::info('binary file download started');

			return $rsp;
		}

		Log::info(
			'Execution Time',
			$this->getLogData($request, $rsp)
		);

		return $rsp;
	}

	public function terminate(Request $request, Response $response)
	{
		if (! ($response instanceof BinaryFileResponse)) {
			return;
		}

		Log::info(
			'Binary File Download Execution Time',
			$this->getLogData($request, $response)
		);
	}

	private function getLogData(Request $request, Response $response): array
	{
		// LARAVEL_START is not defined when request is handled by an octane worker
		$st = defined('LARAVEL_START') ? LARAVEL_START : config('xtra.request.start_time', null);
		$et = empty($st) ? -1 : round(microtime(true) - $st, 3);

		$data = [
			'method' => $request->method(),
			'path' => $request->path(),
			'status' => $response->getStatusCode(),
			'time' => $et,
			'queries' => Stats::getQueryCount(),
		];

		if ($workerCount = config('xtra.octane.worker.request_count', null)) {
			$data['octane_worker_request_count'] = $workerCount;
		}

		return $data;
	}
}
