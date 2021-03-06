<?php

namespace Soluble\FlexStore;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-10-01 at 11:53:48.
 */
class OptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->options = new Options;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    public function testSetGetLimitAndOffset()
    {
        $this->assertNull($this->options->getLimit());
        $this->assertFalse($this->options->hasLimit());
        $this->options->setLimit(10);
        $this->assertEquals(10, $this->options->getLimit());
        $this->assertTrue($this->options->hasLimit());
        
        $this->assertNull($this->options->getOffset());
        $this->assertFalse($this->options->hasOffset());
        
        $this->options->setLimit(40, 50);
        $this->assertEquals(50, $this->options->getOffset());
        $this->options->setLimit(10);
        $this->assertEquals(50, $this->options->getOffset());
        $this->options->unsetOffset();
        $this->assertNull($this->options->getOffset());
        $this->assertFalse($this->options->hasOffset());
        
        $this->options->unsetLimit();
        $this->assertNull($this->options->getLimit());
        $this->assertFalse($this->options->hasLimit());
    }
    
    public function testSetLimitThrowsInvalidException()
    {
        $this->setExpectedException('Soluble\FlexStore\Exception\InvalidArgumentException');
        $this->options->setLimit(array('cool'));
    }

    public function testSetLimitThrowsInvalidException2()
    {
        $this->setExpectedException('Soluble\FlexStore\Exception\InvalidArgumentException');
        $this->options->setLimit(2.12);
    }
    
    public function testSetLimitThrowsInvalidException3()
    {
        $this->setExpectedException('Soluble\FlexStore\Exception\InvalidArgumentException');
        $this->options->setLimit(null);
    }

    
    public function testSetLimitThrowsInvalidException4()
    {
        $this->setExpectedException('Soluble\FlexStore\Exception\InvalidArgumentException');
        $this->options->setLimit(10, array('cool'));
    }

    public function testSetLimitThrowsInvalidException5()
    {
        $this->setExpectedException('Soluble\FlexStore\Exception\InvalidArgumentException');
        $this->options->setLimit(10, 2.12);
    }
}
