<?php

namespace MolnApps\ActiveRecord\Testing;

use \MolnApps\ActiveRecord\Contracts\ResultSet;

use \MolnApps\Repository\Contracts\CollectionFactory;

class Collection implements CollectionFactory, ResultSet, \Iterator, \Countable
{
	private $position = 0;

	private $rows;

	public function __construct(array $rows = [])
	{
		$this->rows = $rows;
	}

	// ! CollectionFactory interface

	public function createCollection(array $rows)
	{
		return new static($rows);
	}

	// ! ResultSet interface

	public function hasResults()
	{
		return ! $this->isEmpty();
	}

	public function getFirstResult()
	{
		return $this->getFirst();
	}

	// ! Countable

	public function count()
	{
		return count($this->rows);
	}

	// ! Iterator

	public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->getObject($this->position);
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->raw[$this->position]);
    }

    // ! Interface

	public function isEmpty()
	{
		return $this->count() == 0;
	}

	public function getFirst()
	{
		return $this->getObject(0);
	}

	private function getObject($index)
	{
		if ( ! isset($this->rows[$index])) {
			return;
		}

		if ( ! isset($this->objects[$index])) {
			$this->objects[$index] = $this->createObject($index);
		}

		return $this->objects[$index];
	}

	private function createObject($index)
	{
		return new Report($this->rows[$index]);
	}
}