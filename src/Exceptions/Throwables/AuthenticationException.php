<?php

namespace DistributedLaravel\Infrastructure\Exceptions\Throwables;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthenticationException extends HttpException
{
	public function __construct(\Throwable $previous = null, $code = 0)
	{
		parent::__construct(401, $previous ? $previous->getMessage() : 'Unauthenticated.', $previous, [], $code);
	}
}
