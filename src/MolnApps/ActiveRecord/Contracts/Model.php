<?php

namespace MolnApps\ActiveRecord\Contracts;

interface Model
{
	public function getModel();

	public function setCreatedAt();
	public function setUpdatedAt();
	public function setDeletedAt();
	public function resetDeletedAt();
}