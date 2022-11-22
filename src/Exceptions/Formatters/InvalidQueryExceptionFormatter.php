<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Formatters;

use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\Exceptions\InvalidAppendQuery;
use Spatie\QueryBuilder\Exceptions\InvalidDirection;
use Spatie\QueryBuilder\Exceptions\InvalidFieldQuery;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;
use Spatie\QueryBuilder\Exceptions\InvalidFilterValue;
use Spatie\QueryBuilder\Exceptions\InvalidIncludeQuery;
use Throwable;

class InvalidQueryExceptionFormatter extends HttpExceptionFormatter
{
	public function format(JsonResponse $response, Throwable $e, array $reporterResponses)
	{
		parent::format($response, $e, $reporterResponses);

		$message = null;

		if ($this->debug) {
			$message = $e->getMessage();
		} else {
			$class = get_class($e);
			$rawMessages = [
				InvalidFilterValue::class,
				InvalidDirection::class,
			];

			if (in_array($class, $rawMessages)) {
				$message = $e->getMessage();
			} else {
				$stuff = match ($class) {
					InvalidFilterQuery::class => ['filter', $e->unknownFilters],
					InvalidIncludeQuery::class => ['include', $e->unknownIncludes],
					InvalidSortQuery::class => ['sort', $e->unknownSorts],
					InvalidAppendQuery::class => ['append', $e->appendsNotAllowed],
					InvalidFieldQuery::class => ['field', $e->unknownFields],

					default => null,
				};

				if ($stuff !== null) {
					list($type, $invalids) = $stuff;
					$invalids = $invalids?->implode(', ');
					$message = "Requested $type(s) `{$invalids}` are not allowed.";
				}
			}
		}

		$response->setStatusCode(400);
		$response->setData([
			'error' => [
				'code' => 'request.query.invalid',
				'message' => $message ?? 'Invalid query',
			],
		]);

		return $response;
	}
}
