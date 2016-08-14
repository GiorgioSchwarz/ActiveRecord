<?php

namespace MolnApps\ActiveRecord;

use \MolnApps\ActiveRecord\Contracts\RepositoryAdapter;
use \MolnApps\ActiveRecord\Contracts\Model;
use \MolnApps\ActiveRecord\Contracts\ModelMap;
use \MolnApps\ActiveRecord\Contracts\ResultSet;

use \MolnApps\Repository\Repository;

class BaseRepository implements RepositoryAdapter
{
	private $repository;
	private $modelMap;

	public function __construct(Repository $repository, ModelMap $modelMap)
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
		$this->repository->save($model->getModel());
	}

	public function delete(Model $model)
	{
		$this->repository->delete($model->getModel());
	}

	public function find()
	{
		$resultSet = $this->repository->find();

		$this->guardResultSet($resultSet);
		
		return $resultSet;
	}

	public function findById($id)
	{
		$resultSet = $this->repository->where([$this->modelMap->getPrimaryKey() => $id])->find();

		$this->guardResultSet($resultSet);

		if ($resultSet->hasResults()) {
			return $resultSet->getFirstResult();
		}
	}

	private function guardResultSet($resultSet)
	{
		if ( ! $resultSet instanceof ResultSet) {
			throw new \Exception('ActiveRecord results must implement ResultSet interface');
		}
	}
}