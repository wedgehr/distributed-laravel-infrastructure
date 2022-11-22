<?php

namespace DistributedLaravel\Infrastructure\Console;

use Illuminate\Console\Command as ConsoleCommand;

class Command extends ConsoleCommand
{
	public function infof(string $format, ...$params)
	{
		return $this->info(sprintf($format, ...$params));
	}

	public function linef(string $format, ...$params)
	{
		return $this->line(sprintf($format, ...$params));
	}

	public function commentf(string $format, ...$params)
	{
		return $this->comment(sprintf($format, ...$params));
	}

	public function questionf(string $format, ...$params)
	{
		return $this->question(sprintf($format, ...$params));
	}

	public function confirmf(string $format, ...$params)
	{
		return $this->confirm(sprintf($format, ...$params));
	}

	public function error($string, $verbosity = null)
	{
		$this->line(sprintf('<fg=red>%s</>', $string), $verbosity);
	}

	public function errorf(string $format, ...$params)
	{
		return $this->error(sprintf($format, ...$params));
	}

	public function warnf(string $format, ...$params)
	{
		return $this->warn(sprintf($format, ...$params));
	}

	public function alertf(string $format, ...$params)
	{
		return $this->alert(sprintf($format, ...$params));
	}

	public function fatalf(string $format = 'fatal error.', ...$params)
	{
		$this->fatal(sprintf($format, ...$params));
	}

	public function fatal(string $msg = 'fatal error.')
	{
		$this->error($msg);
		exit(1);
	}

	public function abort(int $code = 0, string $msg = 'aborted.')
	{
		$this->warn($msg);
		exit($code);
	}

	public function success(string $msg = 'Success!')
	{
		$this->info($msg);
		exit(0);
	}
}
