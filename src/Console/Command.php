<?php

namespace DistributedLaravel\Infrastructure\Console;

use Illuminate\Console\Command as ConsoleCommand;

class Command extends ConsoleCommand
{
	public function infof(string $format, ...$params): void
	{
		$this->info(sprintf($format, ...$params));
	}

	public function linef(string $format, ...$params): void
	{
		$this->line(sprintf($format, ...$params));
	}

	public function commentf(string $format, ...$params): void
	{
		$this->comment(sprintf($format, ...$params));
	}

	public function questionf(string $format, ...$params): void
	{
		$this->question(sprintf($format, ...$params));
	}

	public function confirmf(string $format, ...$params): bool
	{
		return $this->confirm(sprintf($format, ...$params));
	}

	public function error($string, $verbosity = null): void
	{
		$this->line(sprintf('<fg=red>%s</>', $string), $verbosity);
	}

	public function errorf(string $format, ...$params): void
	{
		$this->error(sprintf($format, ...$params));
	}

	public function warnf(string $format, ...$params): void
	{
		$this->warn(sprintf($format, ...$params));
	}

	public function alertf(string $format, ...$params): void
	{
		$this->alert(sprintf($format, ...$params));
	}

	public function fatalf(string $format = 'fatal error.', ...$params): void
	{
		$this->fatal(sprintf($format, ...$params));
	}

	public function fatal(string $msg = 'fatal error.'): void
	{
		$this->error($msg);
		exit(1);
	}

	public function abort(int $code = 0, string $msg = 'aborted.'): void
	{
		$this->warn($msg);
		exit($code);
	}

	public function success(string $msg = 'Success!'): void
	{
		$this->info($msg);
		exit(0);
	}
}
