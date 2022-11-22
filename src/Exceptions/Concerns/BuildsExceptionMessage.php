<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Concerns;

trait BuildsExceptionMessage
{
	/**
	 * @param string $code
	 * @param string $message
	 * @param  array<array-key, mixed> $context
	 */
	protected static function buildExceptionMessage(
		string $code = 'error',
		string $message = 'Error.',
		array $context = [],
	): string {
		$c = compact('code', 'message', 'context');

		if (empty($c['context'])) {
			unset($c['context']);
		}

		return json_encode($c) ?: static::DEFAULT_MESSAGE;
	}
}
