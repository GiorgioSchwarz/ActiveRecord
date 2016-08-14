<?php

namespace MolnApps\ActiveRecord\Testing\Populator;

use \MolnApps\Repository\Contracts\Model;
use \MolnApps\Repository\Contracts\Populator;

class SetAccountId implements Populator
{
	public function populate(Model $model, $operation)
	{
		$model->setAssignment('accountId', 1);
	}
}