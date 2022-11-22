<?php

namespace DistributedLaravel\Infrastructure\Database\Eloquent;

trait NamesEntities
{
	public function _type(): string
	{
		return $this->table;
	}

	public function getEidAttribute(): string
	{
		return entityId($this);
	}
}
