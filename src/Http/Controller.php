<?php

namespace DistributedLaravel\Infrastructure\Http;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\Controller as DefaultController;

abstract class Controller extends DefaultController
{
	public function response(): ResponseFactory|Response
	{
		return response(...func_get_args());
	}

	public function json(): JsonResponse
	{
		return response()->json(...func_get_args());
	}

	public function success(): JsonResponse
	{
		return $this->json(['success' => true]);
	}

	public function error(string $message, int $status = 500): JsonResponse
	{
		return response()->json([
			'message' => $message,
		], $status);
	}
}
