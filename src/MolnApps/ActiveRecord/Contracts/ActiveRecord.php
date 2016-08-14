<?php

namespace MolnApps\ActiveRecord\Contracts;

interface ActiveRecord
{
	public function save();
	public function delete();
	public function trash();
	public function restore();
	public function touch();

	public static function find();
	public static function findById($id);

	public static function trashById($id);
	public static function restoreById($id);
	public static function deleteById($id);
	public static function touchById($id);
}