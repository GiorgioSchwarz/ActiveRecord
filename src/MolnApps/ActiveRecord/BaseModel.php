<?php

namespace MolnApps\ActiveRecord;

use \MolnApps\ActiveRecord\Contracts\Model;
use \MolnApps\ActiveRecord\Contracts\ModelMap;

use \MolnApps\Repository\Contracts\Model as RepositoryModel;

class BaseModel implements Model
{
	private $model;
	private $modelMap;

	public function __construct(RepositoryModel $model, ModelMap $modelMap)
	{
		$this->model = $model;
		$this->modelMap = $modelMap;
	}

	public function getModel()
	{
		return $this->model;
	}

	public function setCreatedAt()
	{
		if ($this->isNew()) {
			$this->setTimestamp($this->modelMap->getCreatedAtKey());
		}
	}

	public function setUpdatedAt()
	{
		$this->setTimestamp($this->modelMap->getUpdatedAtKey());
	}

	public function setDeletedAt()
	{
		$this->setTimestamp($this->modelMap->getDeletedAtKey());
	}

	public function resetDeletedAt()
	{
		$this->setTimestamp($this->modelMap->getDeletedAtKey(), false);
	}

	private function setTimestamp($key, $bool = true)
	{
		if ( ! $this->useTimestmaps()) {
			return;
		}

		$value = ($bool) ? $this->getFreshTimestamp() : null;
		
		$this->setAssignment($key, $value);
	}

	private function useTimestmaps()
	{
		return $this->modelMap->useTimestamps();
	}

	private function getFreshTimestamp()
	{
		return gmdate('Y-m-d H:i:s');
	}

	private function isNew()
	{
		return $this->model->isNew();
	}

	private function setAssignment($name, $value)
	{
		return $this->model->setAssignment($name, $value);
	}
}