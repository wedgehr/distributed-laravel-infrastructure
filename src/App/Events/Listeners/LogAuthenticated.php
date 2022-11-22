<?php

namespace DistributedLaravel\Infrastructure\App\Events\Listeners;

use Illuminate\Auth\Events\Authenticated as Event;
use Illuminate\Support\Facades\Log;

class LogAuthenticated
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
		Log::info('Authenticated', [
			'guard' => $event->guard,
			'user' => $event->user->id,
		]);
	}
}
