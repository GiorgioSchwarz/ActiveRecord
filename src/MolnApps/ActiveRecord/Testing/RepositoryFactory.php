<?php

namespace MolnApps\ActiveRecord\Testing;

use \MolnApps\ActiveRecord\BaseRepository;
use \MolnApps\ActiveRecord\BaseModel;

use \MolnApps\ActiveRecord\Contracts\ModelMap;

use \MolnApps\Repository\Repository;
use \MolnApps\Repository\Contracts\Table;
use \MolnApps\Repository\Contracts\CollectionFactory;

class RepositoryFactory
{
	private static $table;
	private static $collectionFactory;

	public static function createRepository(ModelMap $modelMap, Repository $repository = null)
	{
		$repository = ($repository) ?: new Repository(static::$table, static::$collectionFactory);
		
		return new BaseRepository($repository, $modelMap);
	}

	public static function createModel(AttributeCollector $attributeCollector, ModelMap $modelMap) {
		$model = new Model($attributeCollector, $modelMap);
		return new BaseModel($model, $modelMap);
	}

	public static function reset()
	{
		static::setTable(null);
		static::setCollectionFactory(null);
	}

	public static function setTable(Table $table = null)
	{
		static::$table = $table;
	}

	public static function setCollectionFactory(CollectionFactory $collectionFactory = null)
	{
		static::$collectionFactory = $collectionFactory;
	}
}