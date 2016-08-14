<?php

namespace MolnApps\ActiveRecord;

trait ActiveRecordTrait
{
	private $activeRecord;
	
	// ! Active record

	public function save()
	{
		return $this->getActiveRecord()->save();
	}

	public function trash()
	{
		return $this->getActiveRecord()->trash();
	}

	public function restore()
	{
		return $this->getActiveRecord()->restore();
	}

	public function touch()
	{
		return $this->getActiveRecord()->touch();
	}

	public function delete()
	{
		return $this->getActiveRecord()->delete();
	}

	// ! Static

	public static function find()
	{
		return static::activeRecord()->find();
	}

	public static function findById($id)
	{
		return static::activeRecord()->findById($id);
	}

	public static function trashById($id)
	{
		return static::activeRecord()->trashById($id);
	}

	public static function restoreById($id)
	{
		return static::activeRecord()->restoreById($id);
	}

	public static function touchById($id)
	{
		return static::activeRecord()->touchById($id);
	}

	public static function deleteById($id)
	{
		return static::activeRecord()->deleteById($id);
	}

	private static function activeRecord()
	{
		return (new static)->getActiveRecord();
	}

	private function getActiveRecord()
	{
		if ( ! $this->activeRecord) {
			$this->activeRecord = new ActiveRecord(
				$this->getModel(), 
				$this->getRepository()
			);
		}

		return $this->activeRecord;
	}

	abstract protected function getModel();
	abstract protected function getRepository();
}