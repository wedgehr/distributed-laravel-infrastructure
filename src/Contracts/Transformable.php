<?php

namespace DistributedLaravel\Infrastructure\Contracts;

interface Transformable
{
	/**
	 * @return string
	 */
	public function getTransformerName(): string;

	/**
	 * @return string
	 */
	public static function getDefaultTransformerName(): string;
}
