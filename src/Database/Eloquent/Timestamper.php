<?php

namespace DistributedLaravel\Infrastructure\Database\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait Timestamper
{
	protected function touchTimestamp(
		bool $value,
		string $property,
		string $atProperty = null,
		Carbon $when = null
	): void {
		$atProperty = $atProperty ?? sprintf('%s_at', $property);
		$when = $when ?? now();

		if (! array_key_exists($property, $this->attributes)) {
			$this->attributes[$property] = $value;
		}

		if ($value && ! $this->attributes[$property]) {
			$this->attributes[$atProperty] = $when;
		} elseif (! $value && $this->attributes[$property]) {
			$this->attributes[$atProperty] = null;
		} else {
			return;
		}

		Log::debug('set timestamp property', [
			'model' => get_class($this),
			'id' => $this->id,
			'property' => $atProperty,
			'time' => $when,
		]);
	}
}
