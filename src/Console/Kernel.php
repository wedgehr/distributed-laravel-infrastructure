<?php

namespace DistributedLaravel\Infrastructure\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
	protected function commands()
	{
		Log::debug('loading artisan commands');
	}
}
