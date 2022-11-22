<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Throwables;

use RuntimeException;
use Illuminate\Support\Facades\Log;
use DistributedLaravel\Infrastructure\Exceptions\Concerns\BuildsExceptionMessage;

class AppException extends RuntimeException
{
	use BuildsExceptionMessage;

	protected const DEFAULT_MESSAGE = 'unknown error occurred.';

	final public function __construct(string $msg = 'Error.', \Throwable $previous = null)
	{
		parent::__construct($previous ? $previous->getMessage() : $msg, 0, $previous);
	}

	public static function throw(
		string $msg = 'Error.',
		string $code = 'app.error',
		bool $throw = true
	): static {
		$m = static::buildExceptionMessage($code, $msg, []);

		Log::error('throwing AppException', compact($code, $msg));

		$e = new static($m);

		if ($throw) {
			throw $e;
		}

		return $e;
	}

	public static function unsupported(string $msg = 'Unsupported'): static
	{
		return static::throw('op.unsupported', $msg);
	}

	public static function notImplemented(string $msg = 'Unsupported'): static
	{
		return static::throw('op.not_implemented', $msg);
	}

	public static function config(string $msg = 'invalid config'): static
	{
		return static::throw('app.config', $msg);
	}

	public static function badRequest(string $msg = 'bad request'): BadRequestException
	{
		throw $e = new BadRequestException($msg);

		/** @phpstan-ignore-next-line */
		return $e;
	}

	public static function forbidden(): ForbiddenException
	{
		throw $e = new ForbiddenException();

		/** @phpstan-ignore-next-line */
		return $e;
	}
}
