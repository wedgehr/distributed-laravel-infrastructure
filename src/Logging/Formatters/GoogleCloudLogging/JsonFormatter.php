<?php

namespace DistributedLaravel\Infrastructure\Logging\Formatters\GoogleCloudLogging;

use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;

class JsonFormatter extends BaseJsonFormatter
{
	/**
	 * {@inheritDoc}
	 */
	public function format(array $record): string
	{
		$normalized = $this->normalize($record);

		// rename level_name to severity to support GCP
		$normalized['severity'] = $normalized['level_name'];
		unset($normalized['level_name']);

		if (isset($normalized['context']) && $normalized['context'] === []) {
			if ($this->ignoreEmptyContextAndExtra) {
				unset($normalized['context']);
			} else {
				$normalized['context'] = new \stdClass();
			}
		}
		if (isset($normalized['extra']) && $normalized['extra'] === []) {
			if ($this->ignoreEmptyContextAndExtra) {
				unset($normalized['extra']);
			} else {
				$normalized['extra'] = new \stdClass();
			}
		}

		return $this->toJson($normalized, true) . ($this->appendNewline ? "\n" : '');
	}
}
