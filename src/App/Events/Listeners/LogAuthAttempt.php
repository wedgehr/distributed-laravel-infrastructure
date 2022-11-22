<?php

namespace DistributedLaravel\Infrastructure\App\Events\Listeners;

use Illuminate\Auth\Events\Attempt as Event;
use Log;

class LogAuthAttempt
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param  Event  $event
	 * @return void
	 */
	public function handle(Event $event)
	{
		// we are passed credentials here too.

		Log::info('Auth attempt', [
			'guard' => $event->name,
		]);
	}
}
