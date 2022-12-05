<?php

namespace DistributedLaravel\Infrastructure\App\Events\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Attempting as Event;

class LogAuthAttempt
{
	public function handle(Event $event)
	{
		Log::debug('auth attempt', [
			'guard' => $event->guard,
		]);
	}
}
