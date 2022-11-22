<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Cumulati\Monolog\LogContext;
use Infrastructure\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

if (!function_exists('sanitizeQuerySearchValue')) {
	/**
	 * sanitizeQuerySearchValue - sanitize string
	 * @param string $value
	 * @return string
	 */
	function sanitizeQuerySearchValue(string $value): string
	{
		// strip and cleanup
		$s = preg_replace('/%/', '', $value);
		$s = sprintf('%%%s%%', mb_strtolower(trim($s), 'UTF8'));

		return $s;
	}
}

if (!function_exists('money_round')) {
	function money_round($amount): int
	{
		return (int) (floor(round($amount) * 1000) / 1000);
	}
}

if (!function_exists('money_float')) {
	function money_float(int $amount): float
	{
		return $amount / 100;
	}
}

if (!function_exists('money_human')) {
	function money_human(int $amount): string
	{
		return number_format($amount / 100, 2);
	}
}

if (!function_exists('trim_lower')) {
	function trim_lower(string $str): string
	{
		return trim(strtolower($str));
	}
}

if (!function_exists('strip_breaks')) {
	function strip_breaks(string $str): string
	{
		return (string) preg_replace('/(\r\n)|\r|\n/', '', $str);
	}
}

if (!function_exists('implode_breaks')) {
	function implode_breaks(string $glue, $str): string
	{
		$breaks = preg_split('/(\r\n)|\r|\n/', $str);

		if ($breaks === false) {
			$breaks = [];
		}

		$breaks = array_filter($breaks, function ($x) {
			return !empty($x);
		});

		return implode($glue, $breaks);
	}
}

if (!function_exists('timestamp')) {
	function timestamp(string|Carbon $date = null): int
	{
		$date = $date ?? new Carbon();

		if (is_string($date)) {
			$date = Carbon::parse($date);
		}

		return $date->isoFormat('x');
	}
}

if (!function_exists('parse_timestamp')) {
	function parse_timestamp(int $timestamp): Carbon
	{
		return Carbon::createFromTimestampMs($timestamp);
	}
}

if (!function_exists('carbon_string')) {
	function carbon_string(string|Carbon $date = null): ?string
	{
		if (is_string($date)) {
			return $date;
		}

		return empty($date) ? null : (string) $date;
	}
}

if (!function_exists('uuid')) {
	function uuid(): string
	{
		return \Ramsey\Uuid\Uuid::uuid4()->toString();
	}
}

if (!function_exists('suuid')) {
	function suuid(\Ramsey\Uuid\UuidInterface|string|null $uuid = null): string
	{
		if (!$uuid) {
			$uuid = \Ramsey\Uuid\Uuid::uuid4();
		} elseif (is_string($uuid)) {
			$uuid = \Ramsey\Uuid\Uuid::fromString($uuid);
		}

		return \Facades\ShortUUID::encode($uuid);
	}
}

if (!function_exists('decode_suuid')) {
	function decode_suuid(?string $suuid = null): ?string
	{
		try {
			$suuid = preg_replace('/[^[:alnum:]]/', '', $suuid);
			return \Facades\ShortUUID::decode($suuid)->toString();
		} catch (Ramsey\Uuid\Exception\InvalidUuidStringException $e) {
		} catch (Brick\Math\Exception\NumberFormatException $e) {
		}

		return null;
	}
}

if (!function_exists('short_uuid')) {
	function short_uuid(\Ramsey\Uuid\UuidInterface|string|null $uuid = null)
	{
		return suuid($uuid);
	}
}

if (!function_exists('compact_uuid')) {
	function compact_uuid(\Ramsey\Uuid\UuidInterface|string|null $uuid = null)
	{
		return cuuid($uuid);
	}
}

if (!function_exists('cuuid')) {
	function cuuid(\Ramsey\Uuid\UuidInterface|string|null $uuid = null): string
	{
		if (!$uuid) {
			$uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
		}

		$uuid = (string) $uuid;

		return preg_replace('/-/', '', $uuid);
	}
}

if (!function_exists('valid_uuid')) {
	function valid_uuid(string $uuid): bool
	{
		// FIXME: passing in a string eg. "technician" will assert true
		try {
			Ramsey\Uuid\Uuid::fromString($uuid);
			return true;
		} catch (Ramsey\Uuid\Exception\InvalidUuidStringException $e) {
			if (decode_suuid($uuid) !== null) {
				return true;
			}
		}

		return false;
	}
}

if (!function_exists('valid_cuuid')) {
	function valid_cuuid(string $uuid): bool
	{
		if (strlen($uuid) !== 32) {
			return false;
		}

		return valid_uuid($uuid);
	}
}

if (!function_exists('expand_cuuid')) {
	function expand_cuuid(string $uuid): string
	{
		return Ramsey\Uuid\Uuid::fromString($uuid)->toString();
	}
}

if (!function_exists('normalize_street_zip')) {
	function normalize_street_zip(string $street, $zip)
	{
		$addr = implode(', ', [
			implode_breaks(', ', $street),
			str_pad($zip, 5, '0', STR_PAD_LEFT),
		]);

		return trim_lower($addr);
	}
}

if (!function_exists('randBigInt')) {
	function randBigInt(int $digits = 19): string
	{
		if (!$digits) {
			throw new InvalidArgumentException('digits is required');
		}

		$gen = function ($c) {
			return (string) rand(pow(10, $c - 1), pow(10, $c) - 1);
		};

		if ($digits <= 16) {
			return $gen($digits);
		}

		$x = '';
		while (strlen($x) < $digits) {
			$c = $digits - strlen($x);
			$c > 16 && $c = 16;

			$x .= $gen($c);
		}

		return $x;
	}
}

if (!function_exists('rand64')) {
	function rand64(int $min = 1000000000000000, int $max = 9007199254740991): int
	{
		if ($max > 9007199254740991 || $min > 9007199254740991) {
			throw new LogicException('Integer range is beyond what javascript can cope with');
		}

		return rand($min, $max);
	}
}

if (!function_exists('randHex')) {
	function randHex(int $len = 16): string
	{
		$count = (int) ceil($len / 64);

		$ret = '';
		for ($i = 0; $i < $count; $i++) {
			$ret .= hash('sha256', Illuminate\Support\Str::random(32));
		}

		return substr($ret, 0, $len);
	}
}

if (!function_exists('encodeInt')) {
	function encodeInt(int $int): string
	{
		return \Facades\Hashids::encode($int);
	}
}

if (!function_exists('decodeInt')) {
	function decodeInt(string $str): int
	{
		return \Facades\Hashids::decode($str)[0] ??

		throw new \Exception(
			sprintf('failed to decode int: %s', $str)
		);
	}
}

if (!function_exists('array_trim')) {
	function array_trim(array &$array, array $keys): array
	{
		foreach ($keys as $key) {
			if (!Arr::has($array, $key)) {
				continue;
			}

			$array[$key] = trim($array[$key]);
		}

		return $array;
	}
}

if (!function_exists('str_to_stream')) {
	function str_to_stream(string $string)
	{
		$stream = fopen('php://memory', 'r+');
		fwrite($stream, $string);
		rewind($stream);
		return $stream;
	}
}

if (!function_exists('entityId')) {
	function entityId($entity): string
	{
		if (empty($entity->id)) {
			throw new \Exception(
				sprintf(
					'entity of type %s has no id',
					get_class($entity)
				)
			);
		}

		$type = $entity->entityType ?? null;

		if (empty($type)) {
			$parts = explode('\\', get_class($entity));
			$type = strtolower(array_pop($parts));
		}

		return sprintf('%s_%s', $type, encodeInt($entity->id));
	}
}

if (!function_exists('decodeEid')) {
	function decodeEid(string $eid): ?array
	{
		$matches = [];
		if (preg_match('/^([a-z]+)_(.*)$/', $eid, $matches)) {
			return [
				'type' => $matches[1],
				'id' => decodeInt($matches[2]),
			];
		}

		return null;
	}
}

if (!function_exists('base_class')) {
	function base_class(string $class): string
	{
		$t = explode('\\', $class);
		return array_pop($t);
	}
}

if (!function_exists('applyMinMaxBounds')) {
	function applyMinMaxBounds(int $value, ?int $min, ?int $max = null): int
	{
		// if min is defined, and provided is below min, bring up to min
		if ($min !== null && $value < $min) {
			return $min;
		}

		// if a max is defined, and provided is beyond the range, bring down to max
		if ($max !== null && $value > $max) {
			return $max;
		}

		return $value;
	}
}

if (!function_exists('convertArrayKeysToSnakeCase')) {
	function convertArrayKeysToSnakeCase(array $array): array
	{
		$result = [];
		foreach ($array as $key => $value) {
			$key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));

			$result[$key] = $value;
		}

		return $result;
	}
}

if (!function_exists('lc')) {
	function lc(array $context = []): LogContext
	{
		return LogContext::make($context);
	}
}

if (!function_exists('makeTimestamperAttribute')) {
	function makeTimestamperAttribute(string $atProperty): Attribute
	{
		return Attribute::make(
			get: fn ($v, $a) => !empty($a[$atProperty]),
			set: fn (bool $v, $a) => [
				$atProperty => !$v ? null : ($a[$atProperty] ?? now())
			],
		);
	}
}

if (!function_exists('getId')) {
	function getId(int|Model $m): int
	{
		return is_int($m) ? $m : $m->id;
	}
}
