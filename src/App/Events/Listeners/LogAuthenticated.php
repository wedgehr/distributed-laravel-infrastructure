<?php

namespace DistributedLaravel\Infrastructure\App\Events\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Authenticated as Event;

class LogAuthenticated
{
	/**
	 * Handle the event.
	 *
	 * @param  Event  $event
	 * @return void
	 */
	public function handle(Event $event)
	{
		Log::debug('authenticated', [
			'guard' => $event->guard,
			'user' => $event->user->id ?? null,
		]);
	}
}
