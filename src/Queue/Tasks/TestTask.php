<?php

namespace DistributedLaravel\Infrastructure\Queue\Tasks;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TestTask implements ShouldQueue
{
	use Dispatchable;
	use InteractsWithQueue;
	use Queueable;
	use SerializesModels;

	public function handle()
	{
		// throw new \Exception('no cookies for you');
		Log::info('successfully handled TestTask');
	}
}
