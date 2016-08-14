<?php

namespace MolnApps\ActiveRecord\Contracts;

interface ModelMap
{
	public function getTable();
	public function getPrimaryKey();
	public function getForeignKey();
	
	public function getCreatedAtKey();
	public function getUpdatedAtKey();
	public function getDeletedAtKey();
	public function useTimestamps();
}