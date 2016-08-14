<?php

namespace MolnApps\ActiveRecord;

use \MolnApps\ActiveRecord\Testing\Report;
use \MolnApps\ActiveRecord\Testing\RepositoryFactory;
use \MolnApps\ActiveRecord\Testing\Collection;

use \MolnApps\Repository\Contracts\Table;

class ActiveRecordImplementationTest extends \PHPUnit_Framework_TestCase
{
	private $table;
	private $collectionFactory;
	private $report;

	protected function setUp()
	{
		$this->table = $this->createMock(Table::class);
		
		$this->collectionFactory = new Collection;

		$this->report = new Report;

		RepositoryFactory::setTable($this->table);
		RepositoryFactory::setCollectionFactory($this->collectionFactory);
	}

	protected function tearDown()
	{
		RepositoryFactory::reset();
	}

	/** @test */
	public function it_inserts_a_record()
	{
		$this->tableExpectsInsert([
			'report' => 'Lorem ipsum dolor sit amet',
			'accountId' => 1,
			'userId' => 5,
			'createdAt' => $this->getTimestamp(),
			'updatedAt' => $this->getTimestamp(),
		]);
		
		$this->report->setProperty('report', 'Lorem ipsum dolor sit amet');

		$this->report->save();
	}

	/** @test */
	public function it_updates_a_record()
	{
		$this->tableExpectsUpdate([
			'report' => 'Hello world',
			'editorUserId' => 5,
			'updatedAt' => $this->getTimestamp()
		], ['reportId' => 12]);
		
		$this->report->initializeProperties($this->getRow());

		$this->report->setProperty('report', 'Hello world');

		$this->report->save();
	}

	/** @test */
	public function it_touches_a_record()
	{
		$this->tableExpectsTouchUpdate(['reportId' => 12]);
		
		$this->report->initializeProperties($this->getRow());

		$this->report->touch();
	}

	/** @test */
	public function it_trashes_a_record()
	{
		$this->tableExpectsTrashUpdate(['reportId' => 12]);
		
		$this->report->initializeProperties($this->getRow());

		$this->report->trash();
	}

	/** @test */
	public function it_trashes_a_record_by_id_if_one_is_found()
	{
		$this->tableWillReturn([
			$this->getRow(['reportId' => 11]),
		]);

		$this->tableExpectsTrashUpdate(['reportId' => 11]);

		$report = Report::trashById(11);

		$this->assertInstanceOf(Report::class, $report);
	}

	/** @test */
	public function it_wont_trashe_a_record_by_id_if_none_is_found()
	{
		$this->tableWillReturn([]);

		$report = Report::trashById(11);

		$this->assertNull($report);
	}

	/** @test */
	public function it_restores_a_record()
	{
		$this->tableExpectsRestoreUpdate(['reportId' => 12]);
		
		$this->report->initializeProperties($this->getTrashedRow());

		$this->report->restore();
	}

	/** @test */
	public function it_restores_a_report_by_id_if_one_is_found()
	{
		$this->tableWillReturn([
			$this->getTrashedRow(['reportId' => 11]),
		]);

		$this->tableExpectsRestoreUpdate(['reportId' => 11]);
		
		$report = Report::restoreById(11);

		$this->assertInstanceOf(Report::class, $report);
	}

	/** @test */
	public function it_wont_restores_a_report_by_id_if_none_is_found()
	{
		$this->tableWillReturn([]);

		$report = Report::restoreById(11);

		$this->assertNull($report);
	}

	/** @test */
	public function it_retrieves_a_record()
	{
		$this->tableWillReturn([
			$this->getRow(['reportId' => 1]),
			$this->getRow(['reportId' => 2]),
			$this->getRow(['reportId' => 3]),
		]);
		
		$collection = Report::find();

		$this->assertInstanceOf(Collection::class, $collection);
		$this->assertCount(3, $collection);
	}

	/** @test */
	public function it_retrieves_a_record_by_id()
	{
		$this->tableWillReturn([
			$this->getRow(['reportId' => 2]),
		]);
		
		$report = Report::findById(2);

		$this->assertInstanceOf(Report::class, $report);
	}

	/** @test */
	public function it_returns_null_if_a_report_is_not_found()
	{
		$this->tableWillReturn([]);
		
		$report = Report::findById(2);

		$this->assertNull($report);
	}

	// ! Table methods

	private function tableExpectsInsert(array $assignments)
	{
		$this->table->expects($this->once())->method('insert')->with($assignments);
	}

	private function tableExpectsTouchUpdate(array $identity)
	{
		$this->tableExpectsUpdate([
			'editorUserId' => 5,
			'updatedAt' => $this->getTimestamp(),
		], $identity);
	}

	private function tableExpectsTrashUpdate(array $identity)
	{
		$this->tableExpectsUpdate([
			'editorUserId' => 5,
			'updatedAt' => $this->getTimestamp(),
			'deletedAt' => $this->getTimestamp(),
		], $identity);
	}

	private function tableExpectsRestoreUpdate(array $identity)
	{
		$this->tableExpectsUpdate([
			'editorUserId' => 5,
			'updatedAt' => $this->getTimestamp(),
			'deletedAt' => null, 
		], $identity);
	}

	private function tableExpectsUpdate(array $assignments, array $identity)
	{
		$this->table->expects($this->once())->method('update')->with($assignments, $identity);
	}

	private function tableWillReturn(array $rows)
	{
		$this->table
			->expects($this->once())
			->method('executeSelect')
			->willReturn($rows);
	}

	// ! Properties methods

	private function getTrashedRow(array $override = [])
	{
		$default = ['deletedAt' => $this->getTimestamp('yesterday')];

		return $this->getRow(array_merge($default, $override));
	}

	private function getRow(array $override = [])
	{
		$default = [
			'accountId' => 1,
			'userId' => 5,
			'editorUserId' => null,
			'reportId' => 12,
			'report' => 'Lorem ipsum dolor sit amet',
			'createdAt' => $this->getTimestamp('2 weeks ago'),
			'updatedAt' => $this->getTimestamp('1 week ago'),
			'deletedAt' => null,
		];

		return array_merge($default, $override);
	}

	private function getTimestamp($time = 'now')
	{
		return gmdate('Y-m-d H:i:s', strtotime($time));
	}
}