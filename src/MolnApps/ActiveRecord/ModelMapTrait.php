<?php

namespace MolnApps\ActiveRecord;

trait ModelMapTrait
{
	public function getTable()
	{
		
	}
	
	public function getPrimaryKey()
	{
		return 'id';
	}

	public function getForeignKey()
	{
		
	}

	public function getCreatedAtKey()
	{
		return 'created_at';
	}

	public function getUpdatedAtKey()
	{
		return 'updated_at';
	}

	public function getDeletedAtKey()
	{
		return 'deleted_at';
	}

	public function useTimestamps()
	{
		return true;
	}
}