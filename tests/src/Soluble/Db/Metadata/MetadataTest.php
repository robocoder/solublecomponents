<?php

namespace Soluble\Db\Metadata;

use Soluble\Db\Metadata\Source\AbstractSource;
use Soluble\Db\Metadata\Source\MysqlISMetadata;

use Zend\Db\Adapter\Adapter;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-05 at 14:40:25.
 */
class MetadataTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $adapter = \SolubleTestFactories::getDbAdapter();
        $this->metadata = new Metadata($adapter);
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }
 
    public function testGetSource()
    {
        $source = $this->metadata->getSource();
        $this->assertInstanceOf('Soluble\Db\Metadata\Source\AbstractSource', $source);
    }

    public function testGetDbAdapter()
    {
        $adapter = $this->metadata->getDbAdapter();
        $this->assertInstanceOf('Zend\Db\Adapter\Adapter', $adapter);
    }
    
    
    function testConstructThrowsUnsupportedDriverException()
    {
        $this->setExpectedException('Soluble\Db\Metadata\Exception\UnsupportedDriverException');
        
        $config = array(
            'driver' => 'Pdo_Sqlite',
            'database' => 'sqlite::memory:'
        );
        
        $adapter = new Adapter($config);
        
        $metadata = new Metadata($adapter);
    }
}
