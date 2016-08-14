<?php

namespace MolnApps\ActiveRecord\Testing;

class Report extends AbstractDomainObject
{
	public function getPrimaryKey()
	{
		return 'reportId';
	}

	public function getCreatedAtKey()
	{
		return 'createdAt';
	}

	public function getUpdatedAtKey()
	{
		return 'updatedAt';
	}

	public function getDeletedAtKey()
	{
		return 'deletedAt';
	}
}