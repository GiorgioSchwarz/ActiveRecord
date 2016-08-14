<?php

namespace MolnApps\ActiveRecord\Contracts;

interface ResultSet
{
	public function hasResults();
	public function getFirstResult();
}