<?php

namespace DistributedLaravel\Infrastructure\Database\Eloquent;

use Cumulati\Monolog\LogContext;
use Illuminate\Database\QueryException;

trait GeneratesBigIds
{
	/**
	 * @param array<array-key, mixed> $options
	 */
	public function save(array $options = [])
	{
		if ($this->exists || ! $this->keyGeneration) {
			return parent::save($options);
		}

		if (! empty($this->id)) {
			return parent::save($options);
		}

		$lc = null;
		while (true) {
			try {
				$this->id = rand64();

				return parent::save($options);
			} catch (QueryException $e) {
				if ($e->errorInfo === null) {
					throw $e;
				}

				// errorInfo indices
				// REF: https://www.php.net/manual/en/pdo.errorinfo.php
				//   0) SQLSTATE error code (a five characters alphanumeric identifier defined in the ANSI SQL standard).
				//   1) Driver-specific error code.
				//   2) Driver-specific error message.
				list($code, $driverCode, $msg) = $e->errorInfo;

				// https://www.postgresql.org/docs/9.2/errcodes-appendix.html
				$errs = [
					// postgres does not throw 23000 on primary key violation
					// '23000', // integrity_constraint_violation
					'23505', // unique_violation
				];

				$isId = strstr($msg, sprintf('Key (%s)', $this->getKeyName()));

				// loose compare error codes
				if (in_array((string) $code, $errs) && $isId !== false) {
					$lc = $lc ?? new LogContext();

					$lc->info('caught id collision. regenerating', [
						'collision' => $this->id,
						'table' => $this->table,
					]);

					$lc->debug('id collision error info', [
						'code' => $code,
						'driverCode' => $driverCode,
						'message' => $msg,
					]);

					continue;
				}

				throw $e;
			}
		}
	}
}
