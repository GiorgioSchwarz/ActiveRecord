<?php

namespace MolnApps\ActiveRecord;

use \MolnApps\ActiveRecord\Contracts\Model;
use \MolnApps\ActiveRecord\Contracts\ModelMap;

use \MolnApps\Repository\Contracts\Model as RepositoryModel;

class BaseModelTest extends \PHPUnit_Framework_TestCase
{
	private $repositoryModel;
	private $modelMap;

	private $model;

	protected function setUp()
	{
		$this->repositoryModel = $this->createMock(RepositoryModel::class);
		$this->modelMap = $this->createMock(ModelMap::class);

		$this->model = new BaseModel($this->repositoryModel, $this->modelMap);
	}

	/** @test */
	public function it_can_be_instantiated()
	{
		$this->assertNotNull($this->model);
	}

	/** @test */
	public function it_implements_model_interface()
	{
		$this->assertInstanceOf(Model::class, $this->model);
	}

	/** @test */
	public function it_returns_repository_model()
	{
		$this->assertInstanceOf(RepositoryModel::class, $this->model->getModel());
		$this->assertEquals($this->repositoryModel, $this->model->getModel());
	}

	/** @test */
	public function it_sets_created_at_timestamp_if_model_is_new_and_uses_timestamps()
	{
		$this->useTimestamps();
		$this->createdAtKey('created_at');
		$this->modelIsNew();
		
		$this->modelExpectsTimestamp('created_at');
		
		$this->model->setCreatedAt();
	}

	/** @test */
	public function it_wont_set_created_at_timestamp_if_model_is_new_but_does_not_use_timestamps()
	{
		$this->useTimestamps(false);
		$this->createdAtKey('created_at');
		$this->modelIsNew();
		
		$this->modelExpectsNoTimestamp();

		$this->model->setCreatedAt();
	}

	/** @test */
	public function it_wont_set_created_at_timestamp_if_model_uses_timestamps_but_is_not_new()
	{
		$this->useTimestamps();
		$this->createdAtKey('created_at');
		$this->modelExists();
		
		$this->modelExpectsNoTimestamp();

		$this->model->setCreatedAt();
	}

	/** @test */
	public function it_sets_updated_at_timestamp_if_model_exists_and_uses_timestamps()
	{
		$this->useTimestamps();
		$this->updatedAtKey('updated_at');
		$this->modelExists();
		
		$this->modelExpectsTimestamp('updated_at');
		
		$this->model->setUpdatedAt();
	}

	/** @test */
	public function it_sets_updated_at_timestamp_if_model_is_new_and_uses_timestamps()
	{
		$this->useTimestamps();
		$this->updatedAtKey('updated_at');
		$this->modelIsNew();
		
		$this->modelExpectsTimestamp('updated_at');
		
		$this->model->setUpdatedAt();
	}

	/** @test */
	public function it_wont_set_updated_at_timestamp_if_model_exists_but_uses_no_timestamps()
	{
		$this->useTimestamps(false);
		$this->updatedAtKey('updated_at');
		$this->modelExists();
		
		$this->modelExpectsNoTimestamp();
		
		$this->model->setUpdatedAt();
	}

	/** @test */
	public function it_wont_set_updated_at_timestamp_if_model_is_new_but_uses_no_timestamps()
	{
		$this->useTimestamps(false);
		$this->updatedAtKey('updated_at');
		$this->modelIsNew();
		
		$this->modelExpectsNoTimestamp();
		
		$this->model->setUpdatedAt();
	}

	/** @test */
	public function it_sets_deleted_at_timestamp_if_model_exists_and_uses_timestamps()
	{
		$this->useTimestamps();
		$this->deletedAtKey('deleted_at');
		$this->modelExists();
		
		$this->modelExpectsTimestamp('deleted_at');
		
		$this->model->setDeletedAt();
	}

	/** @test */
	public function it_sets_deleted_at_timestamp_if_model_is_new_and_uses_timestamps()
	{
		$this->useTimestamps();
		$this->deletedAtKey('deleted_at');
		$this->modelIsNew();
		
		$this->modelExpectsTimestamp('deleted_at');
		
		$this->model->setDeletedAt();
	}

	/** @test */
	public function it_wont_set_deleted_at_timestamp_if_model_exists_but_uses_no_timestamps()
	{
		$this->useTimestamps(false);
		$this->deletedAtKey('deleted_at');
		$this->modelExists();
		
		$this->modelExpectsNoTimestamp();
		
		$this->model->setDeletedAt();
	}

	/** @test */
	public function it_wont_set_deleted_at_timestamp_if_model_is_new_but_uses_no_timestamps()
	{
		$this->useTimestamps(false);
		$this->deletedAtKey('deleted_at');
		$this->modelIsNew();
		
		$this->modelExpectsNoTimestamp();
		
		$this->model->setDeletedAt();
	}

	/** @test */
	public function it_resets_deleted_at_timestamp_if_model_exists_and_uses_timestamps()
	{
		$this->useTimestamps();
		$this->deletedAtKey('deleted_at');
		$this->modelExists();
		
		$this->modelExpectsTimestamp('deleted_at', null);
		
		$this->model->resetDeletedAt();
	}

	/** @test */
	public function it_resets_deleted_at_timestamp_if_model_is_new_and_uses_timestamps()
	{
		$this->useTimestamps();
		$this->deletedAtKey('deleted_at');
		$this->modelIsNew();
		
		$this->modelExpectsTimestamp('deleted_at', null);
		
		$this->model->resetDeletedAt();
	}

	/** @test */
	public function it_wont_reset_deleted_at_timestamp_if_model_exists_but_uses_no_timestamps()
	{
		$this->useTimestamps(false);
		$this->deletedAtKey('deleted_at');
		$this->modelExists();
		
		$this->modelExpectsNoTimestamp();
		
		$this->model->resetDeletedAt();
	}

	/** @test */
	public function it_wont_reset_deleted_at_timestamp_if_model_is_new_but_uses_no_timestamps()
	{
		$this->useTimestamps(false);
		$this->deletedAtKey('deleted_at');
		$this->modelIsNew();
		
		$this->modelExpectsNoTimestamp();
		
		$this->model->resetDeletedAt();
	}

	// ! Utility methods

	private function useTimestamps($bool = true)
	{
		$this->modelMap->method('useTimestamps')->willReturn($bool);
	}

	private function createdAtKey($key)
	{
		$this->modelMap->method('getCreatedAtKey')->willReturn($key);
	}

	private function updatedAtKey($key)
	{
		$this->modelMap->method('getUpdatedAtKey')->willReturn($key);
	}

	private function deletedAtKey($key)
	{
		$this->modelMap->method('getDeletedAtKey')->willReturn($key);
	}

	private function modelExists()
	{
		$this->modelIsNew(false);
	}

	private function modelIsNew($bool = true)
	{
		$this->repositoryModel->method('isNew')->willReturn($bool);
	}

	private function modelExpectsTimestamp($key, $timestamp = true)
	{
		$timestamp = ($timestamp === true) ? $this->getTimestamp() : $timestamp;
		$this->repositoryModel
			->expects($this->once())
			->method('setAssignment')
			->with($key, $timestamp);
	}

	private function modelExpectsNoTimestamp()
	{
		$this->repositoryModel
			->expects($this->never())
			->method('setAssignment');
	}

	private function getTimestamp()
	{
		return gmdate('Y-m-d H:i:s');
	}
}