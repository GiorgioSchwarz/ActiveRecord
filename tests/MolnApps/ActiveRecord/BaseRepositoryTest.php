<?php

namespace MolnApps\ActiveRecord;

use \MolnApps\ActiveRecord\Contracts\ModelMap;
use \MolnApps\ActiveRecord\Contracts\Model;
use \MolnApps\ActiveRecord\Contracts\ResultSet;
use \MolnApps\ActiveRecord\Contracts\RepositoryAdapter as RepositoryInterface;

use \MolnApps\Repository\Repository as ConcreteRepository;
use \MolnApps\Repository\Contracts\Model as RepositoryModel;

use \MolnApps\ActiveRecord\Testing\Collection;
use \MolnApps\ActiveRecord\Testing\Report;

class BaseRepositoryTest extends \PHPUnit_Framework_TestCase
{
	private $concreteRepository;
	private $modelMap;

	private $repository;

	protected function setUp()
	{
		$this->concreteRepository = $this->createMock(ConcreteRepository::class);
		
		$this->modelMap = $this->createMock(ModelMap::class);
		$this->modelMap->method('getPrimaryKey')->willReturn('id');

		$this->repository = new BaseRepository($this->concreteRepository, $this->modelMap);

		$this->model = $this->createMock(Model::class);
		$this->repositoryModel = $this->createMock(RepositoryModel::class);
		$this->model->method('getModel')->willReturn($this->repositoryModel);
	}

	/** @test */
	public function it_can_be_instantiated()
	{
		$this->assertNotNull($this->repository);
	}

	/** @test */
	public function it_implements_repository_interface()
	{
		$this->assertInstanceOf(RepositoryInterface::class, $this->repository);
	}

	/** @test */
	public function it_saves_a_model_through_concrete_repository()
	{
		$this->repositoryExpectsSave();
		
		$this->repository->save($this->model);
	}

	/** @test */
	public function it_deletes_a_model_through_concrete_repository()
	{
		$this->repositoryExpectsDelete();
		
		$this->repository->delete($this->model);
	}

	/** @test */
	public function it_finds_all_models_through_concrete_repository()
	{
		$rows = [['foo' => 'bar']];

		$this->repositoryWillReturnResultSet($rows);
		
		$collection = $this->repository->find();

		$this->assertInstanceOf(ResultSet::class, $collection);
	}

	/** @test */
	public function it_throws_if_concrete_repository_does_not_return_result_set()
	{
		$rows = [['foo' => 'bar']];

		$this->repositoryWillReturnArray($rows);
		
		$this->setExpectedException(
			\Exception::class, 
			'ActiveRecord results must implement ResultSet interface'
		);

		$collection = $this->repository->find();
	}

	/** @test */
	public function it_finds_a_model_by_id_through_concrete_repository()
	{
		$rows = [['foo' => 'bar']];

		$this->repositoryExpectsWhere(['id' => 11]);
		$this->repositoryWillReturnResultSet($rows);
		
		$collection = $this->repository->findById(11);

		$this->assertInstanceOf(\stdClass::class, $collection);
	}

	/** @test */
	public function it_throws_if_concrete_repository_does_not_return_result_set_on_find_by_id()
	{
		$rows = [['foo' => 'bar']];

		$this->repositoryExpectsWhere(['id' => 11]);
		$this->repositoryWillReturnArray($rows);
		
		$this->setExpectedException(
			\Exception::class, 
			'ActiveRecord results must implement ResultSet interface'
		);

		$collection = $this->repository->findById(11);
	}

	// ! Utility methods

	private function repositoryExpectsSave()
	{
		$this->concreteRepository
			->expects($this->once())
			->method('save')
			->with($this->repositoryModel);
	}

	private function repositoryExpectsDelete()
	{
		$this->concreteRepository
			->expects($this->once())
			->method('delete')
			->with($this->repositoryModel);
	}

	private function repositoryExpectsWhere(array $where)
	{
		$this->concreteRepository
			->expects($this->once())
			->method('where')
			->with($where)
			->will($this->returnSelf());
	}

	private function repositoryWillReturnResultSet(array $rows)
	{
		$resultSet = $this->createResultSet($rows);

		return $this->repositoryWillReturn($resultSet);
	}

	private function createResultSet(array $rows)
	{
		$resultSet = $this->createMock(ResultSet::class);

		$resultSet->method('getFirstResult')->willReturn((object)$rows[0]);
		$resultSet->method('hasResults')->willReturn( !! count($rows[0]));

		return $resultSet;
	}

	private function repositoryWillReturnArray(array $rows)
	{
		return $this->repositoryWillReturn($rows);
	}

	private function repositoryWillReturn($willReturn)
	{
		return $this->concreteRepository
			->expects($this->once())
			->method('find')
			->willReturn($willReturn);
	}
}