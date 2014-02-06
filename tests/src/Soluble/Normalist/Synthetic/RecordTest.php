<?php

namespace Soluble\Normalist\Synthetic;

use Soluble\Db\Metadata\Source;
use Soluble\Db\Metadata\Exception;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Where;
use \Zend\Db\Sql\Predicate;


/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-06 at 12:21:13.
 */
class RecordTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var TableManager
     */
    protected $tableManager;


    /**
     *
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $adapter;

    
    /**
     *
     * @var Table
     */
    protected $table;
    

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->adapter = \SolubleTestFactories::getDbAdapter();
        
        $cache   = \SolubleTestFactories::getCacheStorage();
        $metadata = new Source\MysqlISMetadata($this->adapter);
        //$metadata->setCache($cache);
        
        $this->tableManager = new TableManager($this->adapter);
        $this->tableManager->setMetadata($metadata);
        
        $this->table = $this->tableManager->table('product_category');
    }
    
    

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->adapter);
        unset($this->tableManager);
        unset($this->table);
    }

    public function testSetDataThrowsInvalidColumnException()
    {
        $medias  = $this->tableManager->table('media');        
        $this->setExpectedException('Soluble\Normalist\Exception\InvalidColumnException');
        $invalid_data = array(
            'coolnotexists' => 'hello'
        );
        $new_record = $medias->newRecord($invalid_data);
    }

    public function testToArray()
    {
        $data = $this->table->find(1)->toArray();
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('category_id', $data);
    }
    
    public function testGetTable()
    {
        $table  = $this->tableManager->table('media');        
        $data   = $this->createMediaRecordData('phpunit_testGetTable');
        $record = $table->insertOnDuplicateKey($data, array('legacy_mapping'));
        
        $returned_table = $record->getTable();
        $this->assertInstanceOf('Soluble\Normalist\Synthetic\Table', $returned_table);
        
        $this->assertEquals($table, $returned_table);
        $this->assertNotEquals($this->table, $returned_table);
        
    }
    
    public function test__Get()
    {
        $medias = $this->table->getTableManager()->table('media');
        $data = $this->createMediaRecordData('phpunit_test__Get');
        $new_record = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $this->assertEquals($new_record->legacy_mapping, 'phpunit_test__Get');
        $this->setExpectedException('Soluble\Normalist\Exception\FieldNotFoundException');
        $a = $new_record->fieldthatnotexists;
    }

    public function test__Set()
    {
        $medias = $this->table->getTableManager()->table('media');
        $data = $this->createMediaRecordData('phpunit_test__Get');
        $new_record = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        
        $new_record->legacy_mapping =  'bibi';
        $this->assertEquals('bibi', $new_record['legacy_mapping']);
        $this->assertEquals('bibi', $new_record->offsetGet('legacy_mapping'));
        $this->assertEquals('bibi', $new_record->legacy_mapping);

        $this->setExpectedException('Soluble\Normalist\Exception\FieldNotFoundException');
        $new_record->fieldthatnotexists = 'biloute';
        
    }
    
    
    
    public function testArrayAccess()
    {
        $medias = $this->table->getTableManager()->table('media');
        $data = $this->createMediaRecordData('phpunit_testArrayAccess');
        $new_record = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $this->assertEquals($new_record['legacy_mapping'], 'phpunit_testArrayAccess');
        
        
        
        $this->setExpectedException('Soluble\Normalist\Exception\FieldNotFoundException');
        $a = $new_record['fieldthatnotexists'];
    }
    
    

    /**
     * @covers Soluble\Normalist\Synthetic\Record::save
     * @todo   Implement testSave().
     */
    public function testSave()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }




    public function testOffsetExists()
    {
        
        $medias = $this->table->getTableManager()->table('media');
        $data = $this->createMediaRecordData('phpunit_test__Get');
        $new_record = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        
        
        $exists = $new_record->offsetExists('fieldthatnotexists');
        $this->assertFalse($exists);
        
        $exists = $new_record->offsetExists('media_id');
        $this->assertTrue($exists);

        /*
         * NOT IN PHP array_key_exists does not handle ->offsetExists
        $exists = array_key_exists('fieldthatnotexists', $new_record);
        $this->assertFalse($exists);

        $exists = array_key_exists('media_id', $new_record);
        $this->assertTrue($exists);
         * 
         */
        
        
    }

    public function testOffsetGet()
    {
        $medias = $this->table->getTableManager()->table('media');
        $data = $this->createMediaRecordData('phpunit_test__Get');
        $new_record = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $this->assertEquals($new_record->offsetGet('legacy_mapping'), 'phpunit_test__Get');
        $this->assertEquals($new_record->offsetGet('legacy_mapping'), $new_record['legacy_mapping']);
        
        $this->setExpectedException('Soluble\Normalist\Exception\FieldNotFoundException');
        $new_record->offsetGet('fieldthatnotexists');
    }
    
    public function testDeleteThrowsLogicException()
    {
        
        $this->setExpectedException('Soluble\Normalist\Exception\LogicException');        
        $medias = $this->table->getTableManager()->table('media');
        $data = $this->createMediaRecordData('phpunit_testDeleteThrowsLogic');
        $media = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $this->assertFalse($media->isDirty());
        $media['legacy_mapping'] = 'cool';
        $this->assertTrue($media->isDirty());        
        $media->delete();
        
    }
    public function testLogicExceptionAfterDelete()
    {
        $medias   = $this->table->getTableManager()->table('media');
        $data     = $this->createMediaRecordData('phpunit_testLogicExceptionAfterDelete');    
        
        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $a = $media->offsetGet('legacy_mapping');
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");
        
        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $a = $media['legacy_mapping'];
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");

        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $a = $media->legacy_mapping;
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");        
        
        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $media['legacy_mapping'] = 'cool';
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");        
        
        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $media->legacy_mapping = 'cool';
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");        

        
        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $media->offsetSet('legacy_mapping', 'cool');
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");        
        
        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $table = $media->getTable();
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");        
        
        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $media->getParent('cool');
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");        
        
        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $media->delete();
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");        

        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $media->save();
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");        

        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $media->isDirty();
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");                
        
        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $media->isDirty();
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");                
        
        
        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $media->offsetExists('legacy_mapping');
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");                
        
        
        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $media->toArray();
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");                
        
        
        // TEST START
        $catched = false;
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media->delete();
        try {
            $media->setData(array());
        } catch (\Soluble\Normalist\Exception\LogicException $e) {
            $catched=true;
        }
        $this->assertTrue($catched, "LogicExceptionAfterDelete works as expected");                
        
        
    }
    public function testDelete()
    {
        $medias   = $this->table->getTableManager()->table('media');
        $data     = $this->createMediaRecordData('phpunit_testDelete');
        $media    = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $media_id = $media->media_id;
        $this->assertFalse($media->isDirty());
        $this->assertTrue($medias->exists($media_id));
        $media->delete();
        
        $this->assertFalse($medias->exists($media_id));
        
        $this->setExpectedException('Soluble\Normalist\Exception\LogicException');        
        $media->delete();
    }
    
    
    public function testIsDirty()
    {
        $medias = $this->table->getTableManager()->table('media');
        $data = $this->createMediaRecordData('phpunit_testIsDirty');
        $media = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $this->assertFalse($media->isDirty());
        $media['legacy_mapping'] = 'cool';
        $this->assertTrue($media->isDirty());

        $media = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $this->assertFalse($media->isDirty());
        $media->offsetSet('legacy_mapping', 'cool');
        $this->assertTrue($media->isDirty());
        
        $media = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $this->assertFalse($media->isDirty());
        $media->legacy_mapping = 'cool';
        $this->assertTrue($media->isDirty());
    }

    public function testOffsetSet()
    {
        
        $medias = $this->table->getTableManager()->table('media');
        $data = $this->createMediaRecordData('phpunit_test__OffsetGet');
        $new_record = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $new_record['legacy_mapping'] = 'cool';
        $this->assertEquals('cool', $new_record['legacy_mapping']);
        $this->assertEquals('cool', $new_record->offsetGet('legacy_mapping'));
        $this->assertEquals('cool', $new_record->legacy_mapping);
        
        $new_record->offsetSet('legacy_mapping',  'bibi');
        $this->assertEquals('bibi', $new_record['legacy_mapping']);
        $this->assertEquals('bibi', $new_record->offsetGet('legacy_mapping'));
        $this->assertEquals('bibi', $new_record->legacy_mapping);
        
    }

    public function testOffsetUnsetThrowsLogicException()
    {
        $this->setExpectedException('Soluble\Normalist\Exception\LogicException');
        $medias = $this->table->getTableManager()->table('media');
        $data = $this->createMediaRecordData('phpunit_test__OffsetUnGet');
        $new_record = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $new_record->offsetUnset('legacy_mapping');
        
    }

    public function testOffsetUnsetThrowsLogicException2()
    {
        $this->setExpectedException('Soluble\Normalist\Exception\LogicException');
        $medias = $this->table->getTableManager()->table('media');
        $data = $this->createMediaRecordData('phpunit_test__OffsetUnGet');
        $new_record = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        unset($new_record['legacy_mapping']);
        
    }    
    
    public function testGetParent()
    {
        
        $data = $this->createMediaRecordData('phpunit_testGetParent');
        $medias = $this->table->getTableManager()->table('media');        
        $media = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));

        $parent = $media->getParent('media_container');
        $this->assertEquals($media->container_id, $parent['container_id']);
        
    }

    public function testGetParentThrowsRelationNotFoundException()
    {
        
        $this->setExpectedException('Soluble\Normalist\Exception\RelationNotFoundException');
        $data = $this->createMediaRecordData('phpunit_testGetParent');
        $medias = $this->table->getTableManager()->table('media');        
        $media = $medias->insertOnDuplicateKey($data, array('legacy_mapping'));
        $parent = $media->getParent('product_category');
        
    }
    
    
    /**
     * Return a media record suitable for database insertion
     * @return array
     */
    protected function createMediaRecordData($legacy_mapping=null)
    {
        $tm = $this->tableManager;
        $container = $tm->table('media_container')->findOneBy(array('reference' => 'PRODUCT_MEDIAS'));
        $container_id = $container['container_id'];
        $data  = array(
            'filename'  => 'phpunit_tablemanager.pdf',
            'filemtime' => 111000,
            'filesize'  => 5000,
            'container_id' => $container_id,
            'legacy_mapping' => $legacy_mapping
        );
        return $data;
    }    
    


}
