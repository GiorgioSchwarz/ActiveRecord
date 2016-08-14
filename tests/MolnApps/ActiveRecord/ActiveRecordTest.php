<?php

namespace MolnApps\ActiveRecord;

use \MolnApps\ActiveRecord\Contracts\Model;
use \MolnApps\ActiveRecord\Contracts\ResultSet;
use \MolnApps\ActiveRecord\Contracts\ActiveRecord as ActiveRecordInterface;
use \MolnApps\ActiveRecord\Contracts\RepositoryAdapter;

class ActiveRecordTest extends \PHPUnit_Framework_TestCase
{
	private $model;
	private $repository;

	private $activeRecordModel;
	private $resultSet;

	private $activeRecord;

	protected function setUp()
	{
		$this->model = $this->createMock(Model::class);
		$this->repository = $this->createMock(RepositoryAdapter::class);

		$this->activeRecordModel = $this->createMock(ActiveRecordInterface::class);
		$this->resultSet = $this->createMock(ResultSet::class);

		$this->activeRecord = new ActiveRecord($this->model, $this->repository);
	}

	/** @test */
	public function it_updates_timestamps_and_saves_record()
	{
		$this->modelExpectsTimestamps('setCreatedAt', 'setUpdatedAt');

		$this->repositoryExpects('save');

		$this->activeRecord->save();
	}

	/** @test */
	public function it_updates_timestamp_and_trashes_existing_record()
	{
		$this->modelExpectsTimestamps('setUpdatedAt', 'setDeletedAt');

		$this->repositoryExpects('save');

		$this->activeRecord->trash();
	}

	/** @test */
	public function it_trashes_record_by_id_if_one_is_found()
	{
		$this->repositoryWillReturnActiveRecordModel(15);

		$this->activeRecordModelExpects('trash');

		$this->activeRecord->trashById(15);
	}

	/** @test */
	public function it_updates_timestamp_and_restores_existing_record()
	{
		$this->modelExpectsTimestamps('setUpdatedAt', 'resetDeletedAt');

		$this->repositoryExpects('save');

		$this->activeRecord->restore();
	}

	/** @test */
	public function it_restores_record_by_id_if_one_is_found()
	{
		$this->repositoryWillReturnActiveRecordModel(12);

		$this->activeRecordModelExpects('restore');

		$this->activeRecord->restoreById(12);
	}

	/** @test */
	public function it_updates_timestamp_and_touches_existing_record()
	{
		$this->modelExpectsTimestamps('setUpdatedAt');

		$this->repositoryExpects('save');

		$this->activeRecord->touch();
	}

	/** @test */
	public function it_touches_record_by_id_if_one_is_found()
	{
		$this->repositoryWillReturnActiveRecordModel(21);

		$this->activeRecordModelExpects('touch');

		$this->activeRecord->touchById(21);
	}

	/** @test */
	public function it_deletes_existing_record()
	{
		$this->repositoryExpects('delete');

		$this->activeRecord->delete();
	}

	/** @test */
	public function it_deletes_existing_record_by_id()
	{
		$this->repositoryWillReturnActiveRecordModel(31);

		$this->activeRecordModelExpects('delete');

		$this->activeRecord->deleteById(31);
	}

	/** @test */
	public function it_throws_if_find_by_id_does_not_return_activeRecord_instance()
	{
		$this->repositoryWillReturnObject(31);

		$this->setExpectedException(
			\Exception::class,
			'Object return by ::findById() must implement ActiveRecord interface'
		);

		$this->activeRecord->deleteById(31);
	}

	/** @test */
	public function it_finds_all_results()
	{
		$this->repositoryWillReturnResultSet();

		$this->repositoryExpectsFind();

		$collection = $this->activeRecord->find();

		$this->assertInstanceOf(ResultSet::class, $collection);
	}

	/** @test */
	public function it_finds_record_by_id()
	{
		$this->repositoryWillReturnActiveRecordModel(11);

		$this->repositoryExpectsFindById();
		
		$record = $this->activeRecord->findById(11);
		
		$this->assertInstanceOf(ActiveRecordInterface::class, $record);
	}

	/** @test */
	public function it_returns_null_if_no_record_is_found()
	{
		$this->repositoryWillReturnNull(11);

		$this->repositoryExpectsFindById();
		
		$record = $this->activeRecord->findById(11);
		
		$this->assertNull($record);
	}

	// ! Utility methods

	private function modelExpectsTimestamps()
	{
		foreach (func_get_args() as $methodName) {
			$this->model->expects($this->once())->method($methodName);
		}
	}

	private function repositoryExpects($method)
	{
		$this->repository->expects($this->once())->method($method)->with($this->model);
	}

	private function repositoryExpectsFind()
	{
		$this->repository->expects($this->once())->method('find');
	}

	private function repositoryExpectsFindById()
	{
		$this->repository->expects($this->once())->method('findById')->with(11);
	}

	private function repositoryWillReturnResultSet()
	{
		$this->repository
			->method('find')
			->willReturn($this->resultSet);
	}

	private function repositoryWillReturnActiveRecordModel($id)
	{
		$this->repository
			->method('findById')
			->with($id)
			->willReturn($this->activeRecordModel);
	}

	private function repositoryWillReturnObject($id)
	{
		$this->repository
			->method('findById')
			->with($id)
			->willReturn((object)['foo' => 'bar']);
	}

	private function repositoryWillReturnNull($id)
	{
		$this->repository
			->method('findById')
			->with($id)
			->willReturn(null);
	}

	private function activeRecordModelExpects($method)
	{
		$this->activeRecordModel->expects($this->once())->method($method);
	}
}