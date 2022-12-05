<?php

namespace DistributedLaravel\Infrastructure\Exceptions;

use Throwable;
use Illuminate\Support\Facades\Log;
use Optimus\Heimdal\ExceptionHandler;

abstract class Handler extends ExceptionHandler
{
	public function register()
	{
	}

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Throwable  $e
	 * @return void
	 */
	public function report(Throwable $e)
	{
		$this->logException($e);

		if (
			$this->shouldReport($e)
			&& ! empty(config('sentry.dsn'))
			&& app()->bound('sentry')
		) {
			Log::info('reporting exception', ['exception' => get_class($e)]);
			app('sentry')->captureException($e);
		}

		parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Throwable  $e
	 * @return \Symfony\Component\HttpFoundation\Response.
	 */
	public function render($request, Throwable $e)
	{
		return parent::render($request, $e);
	}

	public function logException(Throwable $e): void
	{
		$context = array_merge(
			$this->exceptionContext($e),
			[
				'type' => get_class($e),
			]
		);

		Log::error($e->getMessage(), $context);

		Log::debug('uncaught exception', [
			array_merge($context, [
				'message' => $e->getMessage(),
				'exception' => $e,
			]),
		]);
	}
}
