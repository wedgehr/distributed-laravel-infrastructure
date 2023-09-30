<?php

namespace DistributedLaravel\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use DistributedLaravel\Infrastructure\Http\Events\RequestConfigured;

class RequestId
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$extra = [];

		$tid = $request->header('x-transaction-id') ?? null;
		if ($tid) {
			$tid = substr($tid, 0, 36);
		}

		$extra['xtra.transaction.id'] = $tid;

		if ($rid = $request->header('x-request-id') ?? md5((string) Str::uuid())) {
			$rid = substr($rid, 0, 36);
		}

		$extra['xtra.request.id'] = $rid;

		$cip = $request->getClientIp();
		$xff = $request->header('x-forwarded-for');
		$xrip = $request->header('x-real-ip');

		if (! empty($xff)) {
			$cip = $xff;
		} elseif (! empty($xrip)) {
			$cip = $xrip;
		}

		$extra['xtra.client_ip'] = $cip;

		config($extra);

		$ldata = [
			'path' => $request->path(),
			'method' => $request->method(),
		];

		$length = $_SERVER['CONTENT_LENGTH'] ?? null;
		if ($length) {
			$ldata['content_length'] = $length;
		}

		$clientApp = $request->header('x-client-app', null);
		if ($clientApp) {
			$parts = explode('/', $clientApp, 2);

			$ldata['client'] = [
				// trim and slug the app name
				'app' => substr(Str::slug($parts[0]), 0, 16),

				// sometimes we use long git versions, e.g. 1.127.1-27-gf25030f9
				'version' => substr(preg_replace('/[^[:alnum:]\.\-]/', '', $parts[1] ?? ''), 0, 26) ?: null,
			];
		}

		if ($_GET) {
			// log query string if it exists
			$ldata['qs'] = $_GET;
		}

		Log::info('handling request', $ldata);

		Event::dispatch(
			new RequestConfigured(
				requestId: $rid,
				transactionId: $tid,
			)
		);

		$rsp = $next($request);

		$rsp->headers->set('X-Request-Id', $rid);
		$tid && $rsp->headers->set('X-Transaction-Id', $tid);

		return $rsp;
	}
}
