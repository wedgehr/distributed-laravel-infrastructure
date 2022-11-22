<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Formatters;

use Illuminate\Http\JsonResponse;
use Optimus\Heimdal\Formatters\HttpExceptionFormatter as OptimusFormatter;
use Throwable;

class HttpExceptionFormatter extends OptimusFormatter
{
	public function format(JsonResponse $response, Throwable $e, array $reporterResponses)
	{
		parent::format($response, $e, $reporterResponses);

		$decoded = json_decode($e->getMessage(), true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$decoded = [
				'code' => 0,
				'error' => $e->getMessage(),
			];
		}

		$response->setData(['error' => $decoded]);
	}
}
