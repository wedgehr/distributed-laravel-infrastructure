<?php

namespace DistributedLaravel\Infrastructure\App;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

class Stats
{
	protected array $stats = [];

	public function __construct()
	{
		$this->initDbStats();
	}

	public function getDbStats(): ?array
	{
		return $this->stats['db'] ?? null;
	}

	public function getQueryCount(): int
	{
		if (empty($this->stats['db'])) {
			return -1;
		}

		return $this->stats['db']['queryCount'];
	}

	protected function initDbStats()
	{
		$this->stats['db'] = [
			'queryCount' => 0,
			'sql' => [],
		];

		DB::listen(fn ($query) => $this->handleDbEvent($query));
	}

	protected function handleDbEvent(QueryExecuted $query)
	{
		// $query->sql,
		// $query->bindings,
		// $query->time,
		// $query->connectionName,

		++$this->stats['db']['queryCount'];

		if (! array_key_exists($query->sql, $this->stats['db']['sql'])) {
			$this->stats['db']['sql'][$query->sql] = [
				'time' => 0,
				'count' => 0,
			];
		}

		$this->stats['db']['sql'][$query->sql]['time'] += $query->time;
		$this->stats['db']['sql'][$query->sql]['count']++;
	}
}
