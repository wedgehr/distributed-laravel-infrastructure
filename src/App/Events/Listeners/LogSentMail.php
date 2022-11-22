<?php

namespace DistributedLaravel\Infrastructure\App\Events\Listeners;

use Illuminate\Mail\Events\MessageSent as Event;
use Illuminate\Support\Facades\Log;

class LogSentMail
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
		$m = $event->message;

		Log::info('Sent mail message', [
			'to' => $m->getTo(),
			'from' => $m->getFrom(),
			'subject' => $m->getSubject(),
		]);

		// Log::warning('SENT MAIL DATA', $event->data);
	}
}
