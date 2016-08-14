<?php

namespace MolnApps\ActiveRecord\Testing\Populator;

use \MolnApps\Repository\Contracts\Model;
use \MolnApps\Repository\Contracts\Populator;

class SetUserId implements Populator
{
	public function populate(Model $model, $operation)
	{
		$model->setAssignment($this->getKey($operation), 5);
	}

	private function getKey($operation)
	{
		return ($operation == 'insert') ? 'userId' : 'editorUserId';
	}
}