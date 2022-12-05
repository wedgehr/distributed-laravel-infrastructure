<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Formatters;

use Throwable;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\Exceptions\InvalidDirection;
use Spatie\QueryBuilder\Exceptions\InvalidSortQuery;
use Spatie\QueryBuilder\Exceptions\InvalidFieldQuery;
use Spatie\QueryBuilder\Exceptions\InvalidAppendQuery;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;
use Spatie\QueryBuilder\Exceptions\InvalidFilterValue;
use Spatie\QueryBuilder\Exceptions\InvalidIncludeQuery;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidQueryExceptionFormatter extends HttpExceptionFormatter
{
	/**
	 * @param JsonResponse $response
	 * @param HttpException $e
	 */
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
				// /** @var null|array<string, mixed> */
				$ctx = null;
				if ($e instanceof InvalidFilterQuery) {
					$ctx = ['filter', $e->unknownFilters];
				} elseif ($e instanceof InvalidIncludeQuery) {
					$ctx = ['include', $e->unknownIncludes];
				} elseif ($e instanceof InvalidSortQuery) {
					$ctx = ['sort', $e->unknownSorts];
				} elseif ($e instanceof InvalidAppendQuery) {
					$ctx = ['append', $e->appendsNotAllowed];
				} elseif ($e instanceof InvalidFieldQuery) {
					$ctx = ['field', $e->unknownFields];
				}


				if ($ctx !== null) {
					list($type, $invalids) = $ctx;
					$invalids = $invalids->implode(', ');
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
