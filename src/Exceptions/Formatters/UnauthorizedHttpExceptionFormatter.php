<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Formatters;

use Illuminate\Http\JsonResponse;
use Optimus\Heimdal\Formatters\HttpExceptionFormatter;
use Throwable;

class UnauthorizedHttpExceptionFormatter extends HttpExceptionFormatter
{
	public function format(JsonResponse $response, Throwable $e, array $reporterResponses)
	{
		parent::format($response, $e, $reporterResponses);

		return $response;
	}
}
