<?php

namespace MolnApps\ActiveRecord\Testing;

use \MolnApps\Repository\Contracts\Model as ModelInterface;
use \MolnApps\ActiveRecord\Contracts\ModelMap;

class Model implements ModelInterface
{
	private $attributesCollector;
	private $modelMap;

	public function __construct(AttributeCollector $attributesCollector, ModelMap $modelMap)
	{
		$this->attributesCollector = $attributesCollector;
		$this->modelMap = $modelMap;
	}

	// ! Model interface

	public function isNew()
	{
		return ! $this->attributesCollector->hasProperty($this->getPrimaryKey());
	}

	public function isLocked()
	{
		return true;
	}

	public function getAssignments($operation)
	{
		if ($operation == 'insert') {
			$insertProperties = $this->attributesCollector->getProperties();
			unset($insertProperties[$this->getPrimaryKey()]);
			return $insertProperties;
		} else {
			return $this->attributesCollector->getDirtyProperties();
		}
	}

	public function getIdentity()
	{
		return [
			$this->getPrimaryKey() 
				=> $this->attributesCollector->getProperty($this->getPrimaryKey())
		];
	}

	public function setAssignment($name, $value)
	{
		return $this->attributesCollector->setProperty($name, $value);
	}

	public function getAssignment($name)
	{
		return $this->attributesCollector->getProperty($name);
	}

	private function getPrimaryKey()
	{
		return $this->modelMap->getPrimaryKey();
	}
}