<?php

namespace DistributedLaravel\Infrastructure\Http\Events;

use DistributedLaravel\Infrastructure\Events\Event;

class RequestConfigured extends Event
{
	public function __construct(
		public  string $requestId,
		public  ?string $transactionId,
	) {
	}
}
