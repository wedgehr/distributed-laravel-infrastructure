<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Throwables;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundException extends NotFoundHttpException
{
	public static $resource = null;

	public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
	{
		if ($message === null) {
			$message = static::format();
		}

		parent::__construct($message, $previous, $code, $headers);
	}

	public static function throw($id = null, string $type = null)
	{
		throw new static(
			static::format($id, $type)
		);
	}

	private static function format($id = null, string $type = null): string
	{
		if (! $type && static::$resource) {
			$type = static::$resource;
		}

		$msg = 'error not found';
		if ($type) {
			if ($id) {
				$msg = sprintf('%s %s was not found', $type, (string) $id);
			} else {
				$msg = sprintf('%s was not found', $type);
			}
		}

		return json_encode([
			'code' => 404,
			'error' => $msg,
		]);
	}
}
