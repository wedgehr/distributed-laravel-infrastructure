<?php

namespace DistributedLaravel\Infrastructure\App\Events\Listeners;

use Illuminate\Mail\Events\MessageSending as Event;
use Log;

class LogSendingMail
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

		Log::debug('Sending mail message', [
			'to' => $m->getTo(),
			'from' => $m->getFrom(),
			'subject' => $m->getSubject(),
		]);
	}
}
