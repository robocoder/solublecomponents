<?php

namespace Soluble\Normalist;
use Soluble\Db\Metadata\Source;
use Soluble\Db\Metadata\Exception;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-10-03 at 17:28:44.
 */
class SyntheticTableTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var SyntheticTable
	 */
	protected $table;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$adapter = \SolubleTestFactories::getDbAdapter();
		$cache   = \SolubleTestFactories::getCacheStorage();
		$metadata = new Source\MysqlISMetadata($adapter);
		$metadata->setCache($cache);
		
		$this->table = new SyntheticTable($adapter);
		$this->table->setMetadata($metadata);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::select
	 * @todo   Implement testSelect().
	 */
	public function testSelect() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}
	
	

	/**
	 * @covers Soluble\Normalist\SyntheticTable::find
	 */
	public function testFind() {
		$user_id = 1;
		$user = $this->table->find('user', $user_id);
		$this->assertEquals($user_id, $user['user_id']);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::find
	 */
	public function testIdInvalidUsage() {
		$this->setExpectedException('Soluble\Normalist\Exception\InvalidArgumentException');
		
		$this->table->find('user', array('cool', 'test'));
		
		
		//$this->setExpectedException('Soluble\Db\Metadata\Exception\InvalidArgumentException');
		$class = new \stdClass();
		$class->id=1;
		$this->table->find('user', $class);
		  
		 
	}
	
	
	/**
	 * 
	 */
	public function testTableNotExists() {
		//$this->setExpectedException('InvalidArgumentException', 'Invalid magic');
		$this->setExpectedException('Soluble\Db\Metadata\Exception\TableNotExistException');
		$this->table->find("table_that_not_exists", 1);
		
	}
	
	/**
	 * @covers Soluble\Normalist\SyntheticTable::fetchAll
	 * @todo   Implement testFetchAll().
	 */
	public function testFetchAll() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::findOneBy
	 * @todo   Implement testFindOneBy().
	 */
	public function testFindOneBy() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::exists
	 */
	public function testExists() {
		$user_id = 1;
		$this->assertTrue($this->table->exists('user', $user_id));
		$this->assertFalse($this->table->exists('user', 78965465));
		
		
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::delete
	 * @todo   Implement testDelete().
	 */
	public function testDelete() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::insert
	 */
	public function testInsert() {

		$data = $this->createMediaRecordData('phpunit_testInsert');
		$media = $this->table->findOneBy('media', array('legacy_mapping' => $data['legacy_mapping']));
		
		if ($media) {
			$this->table->delete('media', $media['media_id']);
		}
		
		$data['filename'] = 'my_test_filename';
		$return = $this->table->insert('media', $data);
		$this->assertEquals($data['filename'], $return['filename']);
		
		
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::insertOnDuplicateKey
	 */
	public function testInsertOnDuplicateKey() {
		$data = $this->createMediaRecordData('phpunit_testInsertOnDuplicateKeyUpdate');		
		
		$media = $this->table->findOneBy('media', array('legacy_mapping' =>$data['legacy_mapping']));
		
		if ($media) {
			$this->table->delete('media', $media['media_id']);
		}
		
		$record = $this->table->insertOnDuplicateKey('media', $data, array('legacy_mapping'));
		$this->assertTrue($this->table->exists('media', $record['media_id']));
		
		$record['filesize'] = 1000;
		$record->save();
		$this->assertEquals(1000, $record['filesize']);
		
		$record['filesize'] = 5000;
		$record = $this->table->insertOnDuplicateKey('media', $data, array('legacy_mapping'));
		$this->assertEquals(5000, $record['filesize']);

		$record = $this->table->insertOnDuplicateKey('media', $data);
		$this->assertEquals(5000, $record['filesize']);

		$record->delete();

	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::update
	 * @todo   Implement testUpdate().
	 */
	public function testUpdate() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::getRelations
	 * @todo   Implement testGetRelations().
	 */
	public function testGetRelations() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::getColumnsInformation
	 * @todo   Implement testGetColumnsInformation().
	 */
	public function testGetColumnsInformation() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::getPrimaryKeys
	 * @todo   Implement testGetPrimaryKeys().
	 */
	public function testGetPrimaryKeys() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::getPrimaryKey
	 * @todo   Implement testGetPrimaryKey().
	 */
	public function testGetPrimaryKey() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::getMetadata
	 * @todo   Implement testGetMetadata().
	 */
	public function testGetMetadata() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::setDbAdapter
	 * @todo   Implement testSetDbAdapter().
	 */
	public function testSetDbAdapter() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticTable::setTablePrefix
	 * @todo   Implement testSetTablePrefix().
	 */
	public function testSetTablePrefix() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}
	
	/**
	 * Return a media record suitable for database insertion
	 * @return array
	 */
	protected function createMediaRecordData($legacy_mapping=null) {
		$container_id = $this->table->findOneBy('media_container', array('reference' => 'PRODUCT_MEDIAS'))->get('container_id');
		$data  = array(
			'filename'  => 'phpunit_test.pdf',
			'filemtime' => 111000,
			'filesize'  => 5000,
			'container_id' => $container_id,
			'legacy_mapping' => $legacy_mapping
		);
		return $data;
	}	

}
