<?php

namespace MolnApps\ActiveRecord\Testing;

trait AttributeCollectorTrait
{
	private $properties = [];
	private $dirty = [];

	public function initializeProperties(array $properties = [])
	{
		$this->properties = $properties;
		$this->dirty = [];
	}

	// ! DomainObject interface

	public function getProperties(array $keys = [])
	{
		$keys = ($keys) ?: array_keys($this->properties);
		$result = [];
		
		foreach ($keys as $key) {
			$result[$key] = $this->getProperty($key);
		}

		return $result;
	}

	public function getDirtyProperties()
	{
		$result = [];
		
		foreach ($this->dirty as $key => $dirty) {
			$result[$key] = $this->getProperty($key);
		}
		
		return $result;
	}

	public function getProperty($name)
	{
		if ($this->hasProperty($name)) {
			return $this->properties[$name];
		}
	}

	public function hasProperty($name)
	{
		return isset($this->properties[$name]);
	}

	public function setProperty($name, $value)
	{
		if ($this->getProperty($name) != $value) {
			$this->markAsDirty($name);
		}
		
		$this->properties[$name] = $value;
	}

	private function markAsDirty($name)
	{
		$this->dirty[$name] = 1;
	}
}