<?php

namespace MolnApps\ActiveRecord\Testing;

interface AttributeCollector
{
	public function initializeProperties(array $properties = []);

	public function getProperties(array $keys = []);
	public function getDirtyProperties();
	
	public function getProperty($name);
	public function setProperty($name, $value);
	public function hasProperty($name);

	public function getPrimaryKey();
}