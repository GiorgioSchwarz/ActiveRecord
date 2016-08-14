<?php

namespace MolnApps\ActiveRecord\Testing;

use \MolnApps\ActiveRecord\ActiveRecordTrait;
use \MolnApps\ActiveRecord\ModelMapTrait;

use \MolnApps\ActiveRecord\Contracts\ActiveRecord;
use \MolnApps\ActiveRecord\Contracts\RepositoryAdapter;
use \MolnApps\ActiveRecord\Contracts\ModelMap;

use \MolnApps\ActiveRecord\Testing\Populator\SetAccountId;
use \MolnApps\ActiveRecord\Testing\Populator\SetUserId;

abstract class AbstractDomainObject implements AttributeCollector, ActiveRecord, ModelMap
{
	use AttributeCollectorTrait;
	use ActiveRecordTrait;
	use ModelMapTrait;

	public function __construct(array $properties = [])
	{
		$this->initializeProperties($properties);
	}

	// ! Active record trait

	protected function getModel()
	{
		return RepositoryFactory::createModel($this->getAttributeCollector(), $this->getModelMap());
	}

	protected function getRepository()
	{
		$repository = RepositoryFactory::createRepository($this->getModelMap());

		$this->bootRepository($repository);
		
		return $repository;
	}

	protected function bootRepository(RepositoryAdapter $repository)
	{
		$repository->addPopulator('account', new SetAccountId);
		$repository->addPopulator('author', new SetUserId);
	}

	private function getAttributeCollector()
	{
		return $this;
	}

	private function getModelMap()
	{
		return $this;
	}
}