<?php

namespace Soluble\FlexStore\Column;

use Soluble\FlexStore\Source\Zend\SelectSource;
use Zend\Db\Sql\Select;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-04-10 at 15:15:20.
 */
class ColumnModelTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SelectSource
     */
    protected $source;


    /**
     *
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $adapter;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        
        $this->adapter = \SolubleTestFactories::getDbAdapter();
        $select = new \Zend\Db\Sql\Select();
        $select->from('user');
        $params = array(
                'adapter' => $this->adapter,
                'select'  => $select
            );

        $this->source = new SelectSource($params);        
        $this->columnModel = $this->source->getColumnModel();
        
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    public function testGetColumns()
    {
        
        $columnModel = $this->columnModel;
        $this->assertInstanceOf('\Soluble\FlexStore\Column\ColumnModel', $columnModel);
        $columns = $columnModel->getColumns();
        $this->assertInternalType('array', $columns);
    }
    
    public function testExclusion()
    {
        $select = new \Zend\Db\Sql\Select();        
        $select->from('product');
        $params = array(
                'adapter' => $this->adapter,
                'select'  => $select
            );
        $source = new SelectSource($params);        
        $cm = $source->getColumnModel();
        
        $excluded = array('product_id', 'legacy_mapping');
        $cm->setExcluded($excluded);
        $this->assertEquals($excluded, $cm->getExcluded());
        
    }
    
    public function testSortColumns()
    {
        $select = new \Zend\Db\Sql\Select();        
        $select->from('user')->columns(array('user_id', 'password', 'email', 'username'));
        $params = array(
                'adapter' => $this->adapter,
                'select'  => $select
            );
        $source = new SelectSource($params);        
        $cm = $source->getColumnModel();
        
        $sort = array('email', 'user_id');
        $cm->sortColumns($sort);
        
        $this->assertEquals(array('email', 'user_id', 'password', 'username'), $columns);
        
        
    }
  
    public function testIncludeOnly()
    {
        $select = new \Zend\Db\Sql\Select();        
        $select->from('user')->columns(array('user_id', 'email', 'displayName', 'username', 'password'));
        $params = array(
                'adapter' => $this->adapter,
                'select'  => $select
            );
        $source = new SelectSource($params);        
        $cm = $source->getColumnModel();
        
        $include_only = array('email', 'user_id');
        $cm->setIncludeOnly($include_only);

        $this->assertEquals($include_only, $cm->getColumns());
        
    }
      
    
    public function testExclusionRetrieval()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'email', 'displayname', 'username', 'password'));
        $params = array(
                'adapter' => $this->adapter,
                'select'  => $select
            );

        $source = new SelectSource($params);        
        
        $excluded = array('user_id', 'email');
        $cm = $source->getColumnModel();
        $cm->setExcluded($excluded);
        $this->assertEquals($excluded, $cm->getExcluded());
        
        $data = $source->getData();
        $this->isInstanceOf('Soluble\FlexStore\ResultSet\ResultSet');

        $d = $data->toArray();
        $first = array_keys($d[0]);

        $this->assertEquals(3, count($first));
        $this->assertEquals('displayname', array_shift($first));
        $this->assertEquals('username', array_shift($first));
        
    }

    
    public function testGetColumnMeta()
    {

        $select = new \Zend\Db\Sql\Select();        
        $select->from('test_table_types');
        $params = array(
                'adapter' => $this->adapter,
                'select'  => $select
            );

        $source = new SelectSource($params);        
        $columnModel = $source->getColumnModel();
        
        
        $charColumn = $columnModel->getColumnDefinition('test_char_10');
        $this->assertTrue($charColumn->isText());
        $this->assertFalse($charColumn->isDate());
        $this->assertFalse($charColumn->isDatetime());
        $this->assertFalse($charColumn->isNumeric());

        $col = $columnModel->getColumnDefinition('test_float');
        $this->assertFalse($col->isText());
        $this->assertFalse($col->isDate());
        $this->assertFalse($col->isDatetime());
        $this->assertTrue($col->isNumeric());        

        $col = $columnModel->getColumnDefinition('test_date');
        $this->assertFalse($col->isText());
        $this->assertTrue($col->isDate());
        $this->assertFalse($col->isDatetime());
        $this->assertFalse($col->isNumeric());        
        
        
        $col = $columnModel->getColumnDefinition('test_datetime');
        $this->assertFalse($col->isText());
        $this->assertFalse($col->isDate());
        $this->assertTrue($col->isDatetime());
        $this->assertFalse($col->isNumeric());        
        
        $col = $columnModel->getColumnDefinition('test_timestamp');
        $this->assertFalse($col->isText());
        $this->assertFalse($col->isDate());
        $this->assertTrue($col->isDatetime());
        $this->assertFalse($col->isNumeric());
        
        
        
    }
    

}
