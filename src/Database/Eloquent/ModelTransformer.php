<?php

namespace DistributedLaravel\Infrastructure\Database\Eloquent;

trait ModelTransformer
{
	/**
	 * @return string
	 */
	public function getTransformerName(): string
	{
		return $this->transformer ?? static::getDefaultTransformerName();
	}

	/**
	 * @return string
	 */
	public static function getDefaultTransformerName(): string
	{
		$class = preg_replace('/\\\Models\\\/', '\\Transformers\\', get_called_class(), 1);

		return sprintf('%sTransformer', $class);
	}
}
