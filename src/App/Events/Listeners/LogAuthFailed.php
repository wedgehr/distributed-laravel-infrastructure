<?php

namespace DistributedLaravel\Infrastructure\App\Events\Listeners;

use Illuminate\Auth\Events\Failed as Event;
use Log;

class LogAuthFailed
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
		Log::info('Auth failed', [
			'guard' => $event->name,
			'user' => $event->user->id,
		]);
	}
}
