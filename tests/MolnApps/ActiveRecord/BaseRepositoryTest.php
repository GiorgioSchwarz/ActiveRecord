<?php

namespace MolnApps\ActiveRecord;

use \MolnApps\ActiveRecord\Contracts\Repository;
use \MolnApps\ActiveRecord\Contracts\Model;
use \MolnApps\ActiveRecord\Contracts\ModelMap;
use \MolnApps\ActiveRecord\Contracts\ResultSet;

use \MolnApps\ActiveRecord\BaseRepository;
use \MolnApps\ActiveRecord\BaseModel;

use \MolnApps\Repository\Repository as ConcreteRepository;
use \MolnApps\Repository\Contracts\Model as ConcreteModel;

class BaseRepositoryTest extends \PHPUnit_Framework_TestCase
{
	private $concreteRepository;
	private $modelMap;

	private $repository;

	protected function setUp()
	{
		$this->repository = $this->createRepository();

		$this->createModelStub();
	}

	private function createRepository()
	{
		$this->concreteRepository = $this->createMock(ConcreteRepository::class);
		
		$this->modelMap = $this->createMock(ModelMap::class);
		$this->modelMap->method('getPrimaryKey')->willReturn('id');

		return new BaseRepository($this->concreteRepository, $this->modelMap);
	}

	private function createModelStub()
	{
		$this->concreteModel = $this->createMock(ConcreteModel::class);

		$this->model = $this->createMock(BaseModel::class);
		$this->model->method('getModel')->willReturn($this->concreteModel);
	}

	/** @test */
	public function it_can_be_instantiated()
	{
		$this->assertNotNull($this->repository);
	}

	/** @test */
	public function it_implements_repository_interface()
	{
		$this->assertInstanceOf(Repository::class, $this->repository);
	}

	/** @test */
	public function it_saves_a_model_through_concrete_repository()
	{
		$this->repositoryExpectsSave();
		
		$this->repository->save($this->model);
	}

	/** @test */
	public function it_throws_if_model_to_save_is_not_instance_of_base_model()
	{
		$this->expectModelClassException();
		
		$this->repository->save($this->createMock(Model::class));
	}

	/** @test */
	public function it_deletes_a_model_through_concrete_repository()
	{
		$this->repositoryExpectsDelete();
		
		$this->repository->delete($this->model);
	}

	/** @test */
	public function it_throws_if_model_to_delete_is_not_instance_of_base_model()
	{
		$this->expectModelClassException();
		
		$this->repository->delete($this->createMock(Model::class));
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
		
		$this->expectResultSetException();

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
		
		$this->expectResultSetException();

		$collection = $this->repository->findById(11);
	}

	// ! Utility methods

	private function repositoryExpectsSave()
	{
		$this->repositoryExpects('save', $this->concreteModel);
	}

	private function repositoryExpectsDelete()
	{
		$this->repositoryExpects('delete', $this->concreteModel);
	}

	private function repositoryExpectsWhere(array $where)
	{
		$this->repositoryExpects('where', $where)	->will($this->returnSelf());
	}

	private function repositoryExpects($method, $with)
	{
		return $this->concreteRepository
			->expects($this->once())
			->method($method)
			->with($with);
	}

	// ! Repository return methods

	private function repositoryWillReturnResultSet(array $rows)
	{
		$resultSet = $this->createResultSet($rows);

		return $this->repositoryWillReturn($resultSet);
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

	private function createResultSet(array $rows)
	{
		$resultSet = $this->createMock(ResultSet::class);

		$resultSet->method('getFirstResult')->willReturn((object)$rows[0]);
		$resultSet->method('hasResults')->willReturn( !! count($rows[0]));

		return $resultSet;
	}

	// ! Exception methods

	private function expectModelClassException()
	{
		$this->setExpectedException(
			\Exception::class, 
			'MolnApps\ActiveRecord\BaseRepository expects an instance of MolnApps\ActiveRecord\BaseModel to be used'
		);
	}

	private function expectResultSetException()
	{
		$this->setExpectedException(
			\Exception::class, 
			'MolnApps\ActiveRecord\Contracts\Repository expects an instance of MolnApps\ActiveRecord\Contracts\ResultSet as results'
		);
	}
}