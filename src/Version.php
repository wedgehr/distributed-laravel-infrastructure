<?php

namespace DistributedLaravel\Infrastructure;

class Version
{
	protected static array $shortLogExcludedEnvs = [
		'production',
	];

	protected static ?string $apiVersion = null;

	// get app version
	public static function apiVersion(): ?string
	{
		if (static::$apiVersion === null) {
			static::detectApiVersion();
		}

		return static::$apiVersion;
	}

	public static function detectApiVersion(): string
	{
		$versionFile = base_path('version.txt');
		static::$apiVersion = file_exists($versionFile) ? trim(file_get_contents($versionFile)) : exec('git describe --tags');

		config(['app.version' => static::$apiVersion]);

		return static::$apiVersion;
	}
}
