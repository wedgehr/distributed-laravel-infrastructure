<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Formatters;

use Throwable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Optimus\Heimdal\Formatters\UnprocessableEntityHttpExceptionFormatter as OptimusOrig;

class UnprocessableEntityHttpExceptionFormatter extends OptimusOrig
{
	/**
	 * @param JsonResponse $response
	 * @param HttpException $e
	 */
	public function format(JsonResponse $response, Throwable $e, array $reporterResponses)
	{
		parent::format($response, $e, $reporterResponses);

		$response->setStatusCode($e->getStatusCode());
	}
}
