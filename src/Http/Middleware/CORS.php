<?php

namespace DistributedLaravel\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class CORS
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
		$rsp = $next($request);

		$origin = '*';
		$allowCredentials = 'false';

		// if (strpos($request->path(), 'login') === 0) {
		// 	Log::debug('Login route accessed');
		// 	$origin = $request->header('origin');
		// 	$allowCredentials = 'true';
		// }

		$ct = strtolower($rsp->headers->all('Content-Type')[0] ?? '');

		if (strpos($ct, 'text/xml') === 0) {
			$rsp->headers->set('Content-Type', 'text/xml; charset=utf-8');
		} else {
			$rsp->headers->set('Content-Type', 'application/json; charset=utf-8');
		}

		$rsp->headers->set('Access-Control-Allow-Origin', $origin);

		// we dont allow X-Request-Id, as the client should never be generating a requestId. use transactionId for cross service tracing
		$rsp->headers->set('Access-Control-Allow-Headers', 'X-Transaction-Id, X-Client-App, X-XSRF-Token, X-CSRF-Token, X-Requested-With, Content-Type, Authorization, Cache-Control');
		$rsp->headers->set('Access-Control-Expose-Headers', 'X-Request-Id, X-Transaction-Id, Content-Type, Cache-Control, Last-Modified');

		$rsp->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
		$rsp->headers->set('Access-Control-Allow-Credentials', $allowCredentials);

		$rsp->headers->set('Strict-Transport-Security', 'max-age=86400; includeSubDomains');

		$rsp->headers->set('Cache-Control', 'no-store, max-age=0, private, must-revalidate');

		$rsp->headers->set('X-Served-By', config('app.origin'));
		$rsp->headers->set('X-XSS-Protection', '1; mode=block');

		return $rsp;
	}
}
