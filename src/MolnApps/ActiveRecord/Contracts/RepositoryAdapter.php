<?php

namespace MolnApps\ActiveRecord\Contracts;

interface RepositoryAdapter
{
	public function save(Model $model);
	public function delete(Model $model);
	public function find();
	public function findById($id);
}