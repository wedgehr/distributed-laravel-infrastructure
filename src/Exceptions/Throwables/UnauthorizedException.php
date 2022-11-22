<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Throwables;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedException extends HttpException
{
	public function __construct(\Throwable $previous = null, $code = 0)
	{
		parent::__construct(403, $previous ? $previous->getMessage() : 'Unauthorized.', $previous, [], $code);
	}
}
