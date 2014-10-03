<?php

namespace Soluble\FlexStore\Writer\Excel;

use Soluble\FlexStore\Source\Zend\SelectSource;
use Soluble\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use PHPExcel_IOFactory;

class LibXLWriterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var LibXLWriter
     */
    protected $xlsWriter;

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

        if (!extension_loaded('excel')) {
            $this->markTestSkipped(
                    "Excel extension not available."
            );
        } else {

            $this->adapter = \SolubleTestFactories::getDbAdapter();

        }
    }

    /**
     * 
     * @return SelectSource
     */
    protected function getTestSource()
    {

        $select = new Select($this->adapter);
        $select->from(array('p' => 'product'), array())
                ->join(array('ppl' => 'product_pricelist'), 'ppl.product_id = p.product_id', array(), Select::JOIN_LEFT)
                ->join(array('p18' => 'product_translation'), new Expression("p.product_id = p18.product_id and p18.lang = 'fr'"), array(), Select::JOIN_LEFT)
                ->limit(100);

        $select->columns(array(
            'test_chars' => new Expression('"french accents éàùêûçâµè and chinese 请收藏我们的网址"'),
            'product_id' => new Expression('p.product_id'),
            'brand_id' => new Expression('p.brand_id'),
            'reference' => new Expression('p.reference'),
            'description' => new Expression('p.description'),
            'volume' => new Expression('p.volume'),
            'weight' => new Expression('p.weight'),
            'barcode_ean13' => new Expression('1234567890123'),
            'created_at' => new Expression('NOW()'),
            'price' => new Expression('ppl.price'),
            'discount_1' => new Expression('ppl.discount_1'),
            'promo_start_at' => new Expression('ppl.promo_start_at'),
            'promo_end_at' => new Expression('cast(NOW() as date)'),
            'title_fr' => new Expression('p18.title'),
        ));



        $params = array(
            'adapter' => $this->adapter,
            'select' => $select
        );

        $source = new SelectSource($params);

        return $source;
    }

    protected function tearDown()
    {
        
    }

    public function testGetData()
    {
        //$data = $this->xlsWriter->getData();
        //$this->assertInternalType('string', $data);
        $output_file = \SolubleTestFactories::getCachePath() . DIRECTORY_SEPARATOR . 'tmp_phpunit_lbxl_test1.xlsx';

        $source = $this->getTestSource();
        
        $cm = $source->getColumnModel();

        $xlsWriter = new LibXLWriter();
        $xlsWriter->setSource($source);
        
        $xlsWriter->save($output_file);

        $this->assertFileExists($output_file);
        $filesize = filesize($output_file);
        $this->assertGreaterThan(0, $filesize);

        // test Output

        $arr = $this->excelToArray($output_file);
        //$this->assertEquals(113, $arr[5]['B']);
        $this->assertEquals('french accents éàùêûçâµè and chinese 请收藏我们的网址', $arr[2]['A']);
    }

    public function testGetDataWithColumnExclusion()
    {
        $output_file = \SolubleTestFactories::getCachePath() . DIRECTORY_SEPARATOR . 'tmp_phpunit_lbxl_test2.xlsx';

        $source = $this->getTestSource();
        
        $cm = $source->getColumnModel();
        $cm->exclude(array('reference', 'description', 'volume', 'weight', 'barcode_ean13', 'created_at', 'price', 'discount_1', 'promo_start_at', 'promo_end_at'));
        
        $xlsWriter = new LibXLWriter();
        $xlsWriter->setSource($source);
        
        $xlsWriter->save($output_file);
        $this->assertFileExists($output_file);

        // test Output

        $arr = $this->excelToArray($output_file);
        $this->assertEquals(10, $arr[2]['B']);
        $this->assertEquals(173, $arr[2]['C']);
        $this->assertEquals('french accents éàùêûçâµè', trim($arr[2]['D']));
    }
    
    protected function excelToArray($file)
    {
        $excelReader = PHPExcel_IOFactory::createReader('Excel2007');
        $excelFile = $excelReader->load($file);
        $excelFile->setActiveSheetIndex(0);
        $sheet = $excelFile->getActiveSheet();
        $a1 = $sheet->getCellByColumnAndRow('A', '1')->getFormattedValue();

        //var_dump($a1); 
        $arr = $sheet->toArray($nullValue = null, $calculateFormulas = false, $formatData = false, $returnCellRef = true);
        return $arr;
    }
    
    
    /**
     * @covers Soluble\FlexStore\Writer\CSV::send
     * @todo   Implement testSend().
     */
    public function testSend()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
