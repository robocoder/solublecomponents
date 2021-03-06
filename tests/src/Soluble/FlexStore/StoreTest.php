<?php

namespace Soluble\FlexStore;

use Soluble\FlexStore\Source\Zend\SqlSource;
use Soluble\FlexStore\Store;
use Soluble\FlexStore\Column\Column;
use Soluble\FlexStore\Column\ColumnModel;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-10-01 at 15:15:02.
 */
class StoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Adapter
     */
    protected $adapter;
    
    /**
     *
     * @var SqlSource
     */
    protected $source;
    
    /**
     * Dummy select
     * @var Select
     */
    protected $select;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->adapter = \SolubleTestFactories::getDbAdapter();
        $this->source = new SqlSource($this->adapter);
        $this->select = new Select();
        $this->select->from('user');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    public function testBehaviour()
    {
        $source = new SqlSource($this->adapter);
        $source->select()
               ->from(array('ttt' => 'test_table_types'));
        
        $store = new Store($source);
        $cm    = $store->getColumnModel();
        //$config = new Zend\Config\Config();
        //$cm->mergeConfiguration($config);
        $cm->exclude(array('test_multipoint'));
        
        
        $search = $cm->search();
        $search->regexp('/multi/')->setExcluded(true);
        $search->regexp('/^test\_/')->setExcluded(true);
        $search->in(array('test_char_10'))->setExcluded(false);
        
        $data = $store->getData()->toArray();
        $keys = join(',', array_keys($data[0]));
        $this->assertEquals('id,test_char_10', $keys);

        $search->all()->setExcluded(true);
        $search->regexp('/\_10$/')->setExcluded($excluded = false);
        
        $data = $store->getData()->toArray();
        $keys = join(',', array_keys($data[0]));
        $this->assertEquals('test_char_10,test_varbinary_10', $keys);
    }

    public function testGetOptions()
    {
        $this->source->select()->from('product');
        $store = new Store($this->source);
        $options = $store->getOptions();
        $this->assertInstanceOf('Soluble\FlexStore\Options', $options);
        $options->setLimit(2);
        $data = $store->getData()->toArray();
        $this->assertEquals(2, count($data));
    }
    

    public function testGetSource()
    {
        $this->source->select()->from('user');
        $store = new Store($this->source);
        $source = $store->getSource();
        $this->assertInstanceOf('Soluble\FlexStore\Source\Zend\SqlSource', $source);
    }

    public function testGetData()
    {
        $source = $this->source;
        $source->setSelect($this->select);
        $store = new Store($source);
        $resultset = $store->getData();
        $this->assertInstanceOf('Soluble\FlexStore\ResultSet\ResultSet', $resultset);
    }

    public function testGetDataThrowsEmptyQueryException()
    {
        $this->setExpectedException('Soluble\FlexStore\Exception\EmptyQueryException');
        $store = new Store($this->source);
        $resultset = $store->getData();
        $this->assertInstanceOf('Soluble\FlexStore\ResultSet\ResultSet', $resultset);
    }
    
    public function testGetColumnModel()
    {
        $this->source->select()->from('user');
        $store = new Store($this->source);
        $cm = $store->getColumnModel();
        $this->assertInstanceOf('Soluble\FlexStore\Column\ColumnModel', $cm);
    }
}
