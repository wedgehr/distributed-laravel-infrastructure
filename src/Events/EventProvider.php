<?php

namespace DistributedLaravel\Infrastructure\Events;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as EventServiceProvider;

class EventProvider extends EventServiceProvider
{
	/**
	 * We extend the parent listens method so we can
	 * map class array to strings so the array_unique
	 * call in @register is happy
	 */
	public function listens()
	{
		return array_reduce(
			array_keys($this->listen),
			function ($carry, $event) {
				$carry[$event] = array_map(
					fn ($listener) => is_array($listener) ? implode('@', $listener) : $listener,
					$this->listen[$event]
				);

				return $carry;
			},
			[]
		);
	}
}
