<?php

namespace DistributedLaravel\Infrastructure\Logging\Formatters\Default;

use Monolog\Formatter\JsonFormatter;

class Tap
{
	/**
	 * Customize the given logger instance.
	 *
	 * @param  \Illuminate\Log\Logger  $logger
	 * @return void
	 */
	public function __invoke($logger)
	{
		foreach ($logger->getHandlers() as $handler) {
			$handler->setFormatter(
				new JsonFormatter(
					// disable batching. each message should be terminated with a new line
					JsonFormatter::BATCH_MODE_NEWLINES,

					// appendNewLine | append a newLine after each batch
					true,

					// ignoreEmptyContextAndExtra | always include context and extra
					false
				)
			);
		}
	}
}
