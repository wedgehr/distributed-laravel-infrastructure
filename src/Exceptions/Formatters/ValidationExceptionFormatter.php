<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Formatters;

use Throwable;
use Illuminate\Http\JsonResponse;
use Optimus\Heimdal\Formatters\BaseFormatter;

class ValidationExceptionFormatter extends BaseFormatter
{
	public function format(JsonResponse $response, Throwable $e, array $reporterResponses)
	{
		// Laravel validation errors will return JSON string
		$decoded = json_decode($e->getMessage(), true);

		// Message was not valid JSON
		// This occurs when we throw UnprocessableEntityHttpExceptions
		if (json_last_error() !== JSON_ERROR_NONE) {
			// Mimick the structure of Laravel validation errors
			$decoded = ['error' => $e->getMessage()];
		}

		// Laravel errors are formatted as {"field": [/*errors as strings*/]}
		$data = array_reduce(array_keys($decoded), function ($carry, $field) use ($decoded) {
			if (! array_key_exists($field, $carry)) {
				$carry[$field] = [];
			}

			$carry[$field][] = [
				'field' => $field,
				'errors' => $decoded[$field],
			];

			return $carry;
		}, []);

		$response->setStatusCode(422)
			->setData([
				'validationErrors' => $data,
			]);

		return $response;
	}
}
