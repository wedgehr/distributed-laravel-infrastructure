<?php

namespace DistributedLaravel\Infrastructure\Logging\Formatters\Default;

use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\FormattableHandlerInterface;

class Tap
{
	/**
	 * Customize the given logger instance.
	 *
	 * @return void
	 */
	public function __invoke(Logger $logger)
	{
		foreach ($logger->getHandlers() as $handler) {
			if (! $handler instanceof FormattableHandlerInterface) {
				continue;
			}

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
