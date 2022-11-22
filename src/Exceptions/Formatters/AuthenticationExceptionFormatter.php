<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Formatters;

use Illuminate\Http\JsonResponse;
use Optimus\Heimdal\Formatters\HttpExceptionFormatter;
use Throwable;

class AuthenticationExceptionFormatter extends HttpExceptionFormatter
{
	public function format(JsonResponse $response, Throwable $e, array $reporterResponses)
	{
		parent::format($response, $e, $reporterResponses);

		$header = 'Bearer';
		$headers = $e->getHeaders();
		if (array_key_exists('WWW-Authenticate', $headers)) {
			$header = $headers['WWW-Authenticate'];
		}

		$response->headers->set('WWW-Authenticate', $header);

		$response->setData([
			'error' => [
				'code' => 'auth.credentials.invalid',
				'message' => 'invalid auth credentials',
			],
		]);

		return $response;
	}
}
