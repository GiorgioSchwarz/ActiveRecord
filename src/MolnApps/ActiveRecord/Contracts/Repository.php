<?php

namespace MolnApps\ActiveRecord\Contracts;

interface Repository
{
	public function save(Model $model);
	public function delete(Model $model);
	public function find();
	public function findById($id);
}