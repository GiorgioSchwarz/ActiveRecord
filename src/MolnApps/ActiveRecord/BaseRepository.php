<?php

namespace MolnApps\ActiveRecord;

use \MolnApps\ActiveRecord\Contracts\Repository;
use \MolnApps\ActiveRecord\Contracts\Model;
use \MolnApps\ActiveRecord\Contracts\ModelMap;
use \MolnApps\ActiveRecord\Contracts\ResultSet;

use \MolnApps\Repository\Repository as RepositoryImplementation;

class BaseRepository implements Repository
{
	private $repository;
	private $modelMap;

	public function __construct(RepositoryImplementation $repository, ModelMap $modelMap)
	{
		$this->repository = $repository;
		$this->modelMap = $modelMap;
	}

	public function __call($name, $args)
	{
		return call_user_func_array([$this->repository, $name], $args);
	}

	public function save(Model $model)
	{
		$this->guardModelImplementation($model);

		$this->repository->save($model->getModel());
	}

	public function delete(Model $model)
	{
		$this->guardModelImplementation($model);

		$this->repository->delete($model->getModel());
	}

	public function findById($id)
	{
		$where = [$this->modelMap->getPrimaryKey() => $id];
		$this->repository->where($where);

		$resultSet = $this->find();

		if ($resultSet->hasResults()) {
			return $resultSet->getFirstResult();
		}
	}

	public function find()
	{
		$resultSet = $this->repository->find();

		$this->guardResultSet($resultSet);
		
		return $resultSet;
	}

	private function guardResultSet($resultSet)
	{
		if ( ! $resultSet instanceof ResultSet) {
			throw new \Exception(sprintf(
				'%s expects an instance of %s as results',
				Repository::class,
				ResultSet::class
			));
		}
	}

	private function guardModelImplementation($model)
	{
		if ( ! $model instanceof BaseModel) {
			throw new \Exception(sprintf(
				'%s expects an instance of %s to be used', 
				BaseRepository::class,
				BaseModel::class
			));
		}
	}
}