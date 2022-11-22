<?php

namespace DistributedLaravel\Infrastructure\Http\Routing;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\ResponseFactory as IlluminateResponseFactory;

class ResponseFactory extends IlluminateResponseFactory
{
	/**
	 * Create a new JSON response instance.
	 *
	 * We override this method to enable unescaped slashes in json by default.
	 *
	 * @param  mixed  $data
	 * @param  int  $status
	 * @param  array  $headers
	 * @param  int  $options
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function json($data = [], $status = 200, array $headers = [], $options = 0)
	{
		if ($options === 0) {
			$options = JSON_UNESCAPED_SLASHES;
		}

		return new JsonResponse($data, $status, $headers, $options);
	}
}
