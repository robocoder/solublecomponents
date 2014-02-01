<?php

namespace Soluble\Normalist;
use Soluble\Db\Metadata\Source;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-10-04 at 09:01:01.
 */
class SyntheticRecordTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var SyntheticTable
	 */
	protected $table;
	
	/**
	 * @var SyntheticRecord
	 */
	protected $record;

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
	 * @covers Soluble\Normalist\SyntheticRecord::__get
	 */
	function testMagicProperties() {
		$data = $this->createMediaRecordData('phpunit_testMagicProperties');
		$new_record = $this->table->insertOnDuplicateKey('media', $data, array('legacy_mapping'));
		$this->assertEquals($new_record['media_id'], $new_record->media_id);
		
	}	

	/**
	 * @covers Soluble\Normalist\SyntheticRecord::toArray
	 */
	public function testToArray() {
		$data = $this->createMediaRecordData('phpunit_testtoarray');
		$new_record = $this->table->insertOnDuplicateKey('media', $data, array('legacy_mapping'));
		$this->assertInternalType('array', $new_record->toArray());
	}

	/**
	 * @covers Soluble\Normalist\SyntheticRecord::save
	 * @todo   Implement testSave().
	 */
	public function testSave() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticRecord::delete
	 * @todo   Implement testDelete().
	 */
	public function testDelete() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticRecord::get
	 */
	public function testGet() {
		$data = $this->createMediaRecordData('phpunit_testget');
		$new_record = $this->table->insertOnDuplicateKey('media', $data, array('legacy_mapping'));
		$this->assertEquals($new_record->get('legacy_mapping'), 'phpunit_testget');	
		$this->setExpectedException('Soluble\Normalist\Exception\FieldNotFoundException');		
		$new_record->offsetget('fieldthatnotexists');		
	}

	/**
	 * @covers Soluble\Normalist\SyntheticRecord::offsetExists
	 */
	public function testOffsetExists() {
		$data = $this->createMediaRecordData('phpunit_testoffsetexists');
		$new_record = $this->table->insertOnDuplicateKey('media', $data, array('legacy_mapping'));
		$this->assertTrue($new_record->offsetExists('legacy_mapping'));	
	}
	
	
	/**
	 * @covers Soluble\Normalist\SyntheticRecord::offsetGet
	 */
	public function testOffsetGet() {
		$data = $this->createMediaRecordData('phpunit_testoffsetget');
		$new_record = $this->table->insertOnDuplicateKey('media', $data, array('legacy_mapping'));
		$this->assertEquals($new_record->offsetGet('legacy_mapping'), 'phpunit_testoffsetget');	
		$this->setExpectedException('Soluble\Normalist\Exception\FieldNotFoundException');		
		$new_record->offsetGet('fieldthatnotexists');
	}

	/**
	 * @covers Soluble\Normalist\SyntheticRecord::offsetSet
	 */
	public function testOffsetSet() {
		$data = $this->createMediaRecordData('phpunit_testMagicProperties');
		$new_record = $this->table->insertOnDuplicateKey('media', $data, array('legacy_mapping'));
		$new_record->offsetSet('filename', 'cool');
		$this->assertEquals($new_record['filename'], 'cool');		
	}

	/**
	 * @covers Soluble\Normalist\SyntheticRecord::offsetUnset
	 */
	public function testOffsetUnset() {
		$data = $this->createMediaRecordData('phpunit_testMagicProperties');
		$new_record = $this->table->insertOnDuplicateKey('media', $data, array('legacy_mapping'));
		$new_record->offsetUnset('filename');
		$this->setExpectedException('Soluble\Normalist\Exception\FieldNotFoundException');
		$a = $new_record['filename'];		
		
	}

	/**
	 * @covers Soluble\Normalist\SyntheticRecord::getParent
	 */
	public function testGetParent() {

		$data = $this->createMediaRecordData('phpunit_testGetParent');
		
		$record = $this->table->insertOnDuplicateKey('media', $data, array('legacy_mapping'));
		
		$related = $record->getParent('media_container');
		$this->assertEquals($record->container_id, $related['container_id']);			
		
	}

	/**
	 * @covers Soluble\Normalist\SyntheticRecord::__set
	 */
	public function test__set() {
		$data = $this->createMediaRecordData('phpunit_testMagicProperties');
		$new_record = $this->table->insertOnDuplicateKey('media', $data, array('legacy_mapping'));
		$new_record->filename = 'cool';
		$this->assertEquals($new_record['filename'], $new_record->filename);
	}

	/**
	 * @covers Soluble\Normalist\SyntheticRecord::__get
	 */
	public function test__get() {
		$data = $this->createMediaRecordData('phpunit_testMagicProperties');
		$new_record = $this->table->insertOnDuplicateKey('media', $data, array('legacy_mapping'));
		$this->assertEquals($new_record['media_id'], $new_record->media_id);
	}
	
	/**
	 * Return a media record suitable for database insertion
	 * @return array
	 */
	protected function createMediaRecordData($legacy_mapping=null) {
		$this->table->insertOnDuplicateKey('media_container', array('reference' => 'PRODUCT_MEDIAS'));
		
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