<?php

namespace MolnApps\ActiveRecord;

use \MolnApps\ActiveRecord\Contracts\ActiveRecord as ActiveRecordInterface;

use \MolnApps\ActiveRecord\Contracts\Model;
use \MolnApps\ActiveRecord\Contracts\ModelMap;
use \MolnApps\ActiveRecord\Contracts\RepositoryAdapter;

class ActiveRecord
{
	private $model;
	private $repository;

	public function __construct(Model $model, RepositoryAdapter $repository)
	{
		$this->model = $model;
		$this->repository = $repository;
	}

	// ! Active record

	public function find()
	{
		return $this->repository->find();
	}

	public function findById($id)
	{
		return $this->repository->findById($id);
	}

	public function save()
	{
		$this->model->setCreatedAt();
		
		$this->touchAndSave();
	}

	public function delete()
	{
		$this->repository->delete($this->model);
	}

	public function trash()
	{
		$this->model->setDeletedAt();
		
		$this->touchAndSave();
	}

	public function restore()
	{
		$this->model->resetDeletedAt();

		$this->touchAndSave();
	}

	public function touch()
	{
		$this->touchAndSave();
	}

	private function touchAndSave()
	{
		$this->model->setUpdatedAt();

		$this->repository->save($this->model);
	}

	public function deleteById($id)
	{
		return $this->performById('delete', $id);
	}

	public function trashById($id)
	{
		return $this->performById('trash', $id);
	}

	public function restoreById($id)
	{
		return $this->performById('restore', $id);
	}

	public function touchById($id)
	{
		return $this->performById('touch', $id);
	}

	private function performById($method, $id)
	{
		$model = $this->findById($id);
		
		if ( ! $model) {
			return;
		}

		if ( ! $model instanceof ActiveRecordInterface) {
			throw new \Exception('Object return by ::findById() must implement ActiveRecord interface');
		}

		$model->$method();

		return $model;
	}
}