<?php

namespace DistributedLaravel\Infrastructure\App\Events;

use DistributedLaravel\Infrastructure\App\Events\Listeners\LogAuthAttempt;
use DistributedLaravel\Infrastructure\App\Events\Listeners\LogAuthenticated;
use DistributedLaravel\Infrastructure\App\Events\Listeners\LogAuthFailed;
use DistributedLaravel\Infrastructure\App\Events\Listeners\LogSendingMail;
use DistributedLaravel\Infrastructure\App\Events\Listeners\LogSentMail;
use DistributedLaravel\Infrastructure\Events\EventProvider as EP;
use Illuminate\Auth\Events\Attempting as AuthAttempt;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Failed as AuthFailed;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * This event provider logs App Events
 */
class AppEventProvider extends EP
{
	protected $listen = [
		MessageSending::class => [
			LogSendingMail::class,
		],

		MessageSent::class => [
			LogSentMail::class,
		],

		AuthAttempt::class => [
			LogAuthAttempt::class,
		],

		Authenticated::class => [
			LogAuthenticated::class,
		],

		AuthFailed::class => [
			LogAuthFailed::class,
		],
	];

	public function boot()
	{
		parent::boot();

		if ($this->app['config']['app.debug_events']) {
			// ref: https://github.com/barryvdh/laravel-debugbar/blob/master/src/DataCollector/EventCollector.php
			Event::listen('*', [$this, 'onWildcardEvent']);
		}
	}

	public function onWildcardEvent($name = null, $data = [])
	{
		// TIL logging emits events
		if (strpos($name, 'Illuminate\\Log') === 0) {
			return;
		}

		Log::debug('saw event', [
			'name' => $name,
		]);
	}
}
