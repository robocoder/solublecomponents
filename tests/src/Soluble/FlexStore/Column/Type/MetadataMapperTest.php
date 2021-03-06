<?php

namespace Soluble\FlexStore\Column\Type;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-10-17 at 12:02:01.
 */
class MetadataMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    public function testGetColumnTypeByMetadataType()
    {
        $supported = \Soluble\Db\Metadata\Column\Type::getSupportedTypes();
        $mapper = new MetadataMapper();
        foreach ($supported as $md_type) {
            $type = $mapper->getColumnTypeByMetadataType($md_type);
            $this->assertInstanceOf('Soluble\FlexStore\Column\Type\AbstractType', $type);
            $this->assertInternalType('string', $type->getName());
            $this->assertTrue(\Soluble\FlexStore\Column\ColumnType::isSupported($type->getName()));
        }
    }

    public function testGetColumnTypeByMetadataTypeThrowsInvalidArgumentException()
    {
        $this->setExpectedException('Soluble\FlexStore\Column\Exception\InvalidArgumentException');
        $mapper = new MetadataMapper();
        $mapper->getColumnTypeByMetadataType('invalid_type');
    }
}
