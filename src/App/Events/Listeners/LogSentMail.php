<?php

namespace DistributedLaravel\Infrastructure\App\Events\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Events\MessageSent as Event;

class LogSentMail
{
	/**
	 * Handle the event.
	 *
	 * @param  Event  $event
	 * @return void
	 */
	public function handle(Event $event)
	{
		$m = $event->message;

		Log::debug('sent mail message', [
			'to' => $m->getTo(),
			'from' => $m->getFrom(),
			'subject' => $m->getSubject(),
		]);
	}
}
