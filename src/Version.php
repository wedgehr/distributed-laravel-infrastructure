<?php

namespace DistributedLaravel\Infrastructure;

use Symfony\Component\Process\Process;

class Version
{
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
		static::$apiVersion = file_exists($versionFile)
			? trim(file_get_contents($versionFile))
			: static::generateGitVersion();

		if (empty(static::$apiVersion)) {
			static::$apiVersion = 'unknown';
		}

		config(['app.version' => static::$apiVersion]);

		return static::$apiVersion;
	}

	protected static function generateGitVersion(): ?string
	{
		$process = new Process(['git', 'describe', '--tags', '--always']);
		$process->run();

		if (! $process->isSuccessful()) {
			return null;
		}

		$output = trim($process->getOutput());

		return empty($output) ? null : $output;
	}
}
