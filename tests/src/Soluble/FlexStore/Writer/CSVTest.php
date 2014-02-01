<?php

namespace Soluble\FlexStore\Writer;

use Soluble\FlexStore\Source\Zend\SelectSource;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-10-16 at 15:18:12.
 */
class CSVTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var CSV
	 */
	protected $csvWriter;

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
	protected function setUp() {
		$this->adapter = \SolubleTestFactories::getDbAdapter();
		$select = new \Zend\Db\Sql\Select();
		$select->from('product_category_translation')->where("lang = 'fr'")->limit(50);
		$params = array(
			'adapter' => $this->adapter,
			'select' => $select
		);

		$this->source = new SelectSource($params);


		$this->csvWriter = new CSV();
		$this->csvWriter->setSource($this->source);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		
	}

	/**
	 * @covers Soluble\FlexStore\Writer\CSV::getData
	 */
	public function testGetData() {
		
		$data = $this->csvWriter->getData();
		$this->assertInternalType('string', $data);
	}

	/**
	 * @covers Soluble\FlexStore\Writer\CSV::getData
	 */
	public function testGetDataLatin1Charset() {
		
		//die();
		$enclosure = '"';
		$this->csvWriter->setOptions(
				array(
					'field_separator' => CSV::SEPARATOR_TAB,
					'line_separator' => CSV::SEPARATOR_NEWLINE_UNIX,
					'enclosure' => $enclosure,
					'charset' => 'ISO-8859-1'
					//'charset' => 'UTF-8'
					)
				);
		
		$data = $this->csvWriter->getData();
		$this->assertInternalType('string', $data);
		$data = explode(CSV::SEPARATOR_NEWLINE_UNIX, $data);
		$line0 = str_getcsv($data[0], CSV::SEPARATOR_TAB, $enclosure, $escape=null);
		$this->assertInternalType('array', $line0);
		$this->assertEquals($line0[1], 'category_id');

		$select = new \Zend\Db\Sql\Select();
		$select->from('product_category_translation')->where("lang = 'fr' and category_id = 988")->limit(50);
		$params = array(
			'adapter' => $this->adapter,
			'select' => $select
		);

		
		$this->csvWriter->setSource(new SelectSource($params));
		$data = $this->csvWriter->getData();
		$data = explode(CSV::SEPARATOR_NEWLINE_UNIX, $data);
		$line1 = str_getcsv($data[1], CSV::SEPARATOR_TAB, $enclosure, $escape=null);
		$this->assertInternalType('array', $line1);
		$title = $line1[4];
		
		$this->assertTrue(mb_check_encoding($title, 'ISO-8859-1'));
	}

	/**
	 * @covers Soluble\FlexStore\Writer\CSV::getData
	 */
	public function testGetDataUTF8Charset() {
		
		//die();
		$enclosure = '"';
		$this->csvWriter->setOptions(
				array(
					'field_separator' => CSV::SEPARATOR_TAB,
					'line_separator' => CSV::SEPARATOR_NEWLINE_UNIX,
					'enclosure' => $enclosure,
					//'charset' => 'ISO-8859-1'
					)
				);
		
		$data = $this->csvWriter->getData();
		$this->assertInternalType('string', $data);
		$data = explode(CSV::SEPARATOR_NEWLINE_UNIX, $data);
		$line0 = str_getcsv($data[0], CSV::SEPARATOR_TAB, $enclosure, $escape=null);
		$this->assertInternalType('array', $line0);
		$this->assertEquals($line0[1], 'category_id');

		$select = new \Zend\Db\Sql\Select();
		$select->from('product_category_translation')->where("lang = 'fr' and category_id = 988")->limit(50);
		$params = array(
			'adapter' => $this->adapter,
			'select' => $select
		);

		
		$this->csvWriter->setSource(new SelectSource($params));
		$data = $this->csvWriter->getData();
		$data = explode(CSV::SEPARATOR_NEWLINE_UNIX, $data);
		$line1 = str_getcsv($data[1], CSV::SEPARATOR_TAB, $enclosure, $escape=null);
		$this->assertInternalType('array', $line1);
		$title = $line1[4];
		
		$this->assertTrue(mb_check_encoding($title, 'UTF-8'));
	}
	
	
	/**
	 * @covers Soluble\FlexStore\Writer\CSV::getOptions
	 */
	public function testGetDataWithOptionsThrowsInvalidArgumentException() {
		$this->setExpectedException('Soluble\FlexStore\Exception\InvalidArgumentException');
		$this->csvWriter->setOptions(
				array(
					'rossssss' => 'line',
					)
				);

		
		$data = $this->csvWriter->getData();
	}
	

	/**
	 * @covers Soluble\FlexStore\Writer\CSV::getData
	 */
	public function testGetDataEscapeDelimiter() {
		$enclosure = '"';
		$this->csvWriter->setOptions(
				array(
					'field_separator' => CSV::SEPARATOR_SEMICOLON,
					'line_separator' => CSV::SEPARATOR_NEWLINE_UNIX,
					'enclosure' => $enclosure,
					'charset' => 'ISO-8859-1',
					'escape' => '\\'
					)
				);


		$select = new \Zend\Db\Sql\Select();
		$select->from(array('pc18' => 'product_category_translation'))
			   ->columns(array(
				   'category_id',
				   'test' => new \Zend\Db\Sql\Expression("'alpha; beta;'")
			   ))	
			   ->where("lang = 'fr' and category_id = 988");
		$params = array(
			'adapter' => $this->adapter,
			'select' => $select
		);

		
		$this->csvWriter->setSource(new SelectSource($params));
		$data = $this->csvWriter->getData();
		$this->assertContains('alpha\; beta\;', $data);
		
	}
	/**
	 * @covers Soluble\FlexStore\Writer\CSV::send
	 * @todo   Implement testSend().
	 */
	public function testSend() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

}