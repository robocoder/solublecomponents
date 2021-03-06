<?php

namespace Soluble\FlexStore\Column;

use Soluble\FlexStore\Source\Zend\SqlSource;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Soluble\FlexStore\Formatter\CurrencyFormatter;
use Soluble\FlexStore\Store;
use Soluble\FlexStore\Column\Column;
use Soluble\FlexStore\Column\ColumnModel;
use Soluble\FlexStore\Column\ColumnType;
use Soluble\FlexStore\Renderer\ClosureRenderer;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-04-10 at 15:15:20.
 */
class ColumnModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SqlSource
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



        $this->source = new SqlSource($this->adapter, $select);

        $this->columnModel = $this->source->getColumnModel();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testRenderer()
    {
        $source = new SqlSource($this->adapter);
        $select = $source->select();
        $select->from(array('p' => 'product'), array())
                ->join(array('ppl' => 'product_pricelist'), new Expression('ppl.product_id = p.product_id and ppl.pricelist_id = 1'), array(), $select::JOIN_LEFT);

        $select->columns(array(
            'product_id' => new Expression('p.product_id'),
            'reference' => new Expression('p.reference'),
            'price' => new Expression('ppl.price'),
            'list_price' => new Expression('ppl.list_price'),
            'public_price' => new Expression('ppl.public_price'),
            'currency_reference' => new Expression("'CNY'")
        ));

        $store = new Store($source);
        $cm = $store->getColumnModel();

        $f = function (\ArrayObject $row) {
            $row['product_id'] = "My product id:" . $row['product_id'];
        };
        $clo = new ClosureRenderer($f);
        $cm->addRowRenderer($clo);

        $data = $store->getData();
        $this->assertEquals('My product id:10', $data->current()->offsetGet('product_id'));
    }

    public function testRenderer2()
    {
        $source = new SqlSource($this->adapter);
        $select = $source->select();
        $select->from(array('p' => 'product'), array())
                ->join(array('ppl' => 'product_pricelist'), new Expression('ppl.product_id = p.product_id and ppl.pricelist_id = 1'), array(), $select::JOIN_LEFT);

        $select->columns(array(
            'product_id' => new Expression('p.product_id'),
            'reference' => new Expression('p.reference'),
            'price' => new Expression('ppl.price'),
            'list_price' => new Expression('ppl.list_price'),
            'public_price' => new Expression('ppl.public_price'),
            'currency_reference' => new Expression("'CNY'")
        ));

        $store = new Store($source);
        $cm = $store->getColumnModel();
        $column = new Column('cool', array('type' => ColumnType::TYPE_STRING));
        $cm->add($column);

        $this->assertTrue($column->isVirtual());
        $this->assertFalse($cm->get('product_id')->isVirtual());
        


        $f = function (\ArrayObject $row) {
            $row['cool'] = "My cool value is :" . $row['product_id'];
        };
        $clo = new ClosureRenderer($f);
        $clo->setRequiredColumns(array('product_id', 'reference'));
        $cm->addRowRenderer($clo);

        $data = $store->getData();
        $this->assertEquals('My cool value is :10', $data->current()->offsetGet('cool'));
    }

    public function testRenderer3ThrowsException()
    {
        $this->setExpectedException('Exception');
        $source = new SqlSource($this->adapter);
        $select = $source->select();
        $select->from(array('p' => 'product'), array())
                ->join(array('ppl' => 'product_pricelist'), new Expression('ppl.product_id = p.product_id and ppl.pricelist_id = 1'), array(), $select::JOIN_LEFT);

        $select->columns(array(
            'product_id' => new Expression('p.product_id'),
            'reference' => new Expression('p.reference'),
            'price' => new Expression('ppl.price'),
            'list_price' => new Expression('ppl.list_price'),
            'public_price' => new Expression('ppl.public_price'),
            'currency_reference' => new Expression("'CNY'")
        ));

        $store = new Store($source);
        $cm = $store->getColumnModel();
        $column = new Column('cool', array('type' => ColumnType::TYPE_STRING));
        $cm->add($column);

        $f2 = function (\ArrayObject $row) {
            if (!$row->offsetExists('pas_cool')) {
                throw new \Exception("pascool column in row");
            }
            $row['cool'] = "My cool value is :" . $row['product_id'];
        };
        $clo = new ClosureRenderer($f2);
        $cm->addRowRenderer($clo);

        $data = $store->getData()->toArray();
    }

    
    public function testRendererThrowsMissingColumnException()
    {
        $this->setExpectedException('Soluble\FlexStore\Column\Exception\MissingColumnException');
        $source = new SqlSource($this->adapter);
        $select = $source->select();
        $select->from(array('p' => 'product'), array())
                ->join(array('ppl' => 'product_pricelist'), new Expression('ppl.product_id = p.product_id and ppl.pricelist_id = 1'), array(), $select::JOIN_LEFT);

        $select->columns(array(
            'product_id' => new Expression('p.product_id'),
            'reference' => new Expression('p.reference'),
            'price' => new Expression('ppl.price'),
            'list_price' => new Expression('ppl.list_price'),
            'public_price' => new Expression('ppl.public_price'),
            'currency_reference' => new Expression("'CNY'")
        ));

        $store = new Store($source);
        $cm = $store->getColumnModel();
        $column = new Column('cool', array('type' => ColumnType::TYPE_STRING));
        $cm->add($column);

        $f2 = function (\ArrayObject $row) {
            $row['cool'] = "My cool value is :" . $row['product_id'];
        };
        $clo = new ClosureRenderer($f2);
        $clo->setRequiredColumns(array('notexists'));
        $cm->addRowRenderer($clo);

        $data = $store->getData()->toArray();
    }
    
    
    public function testSearch()
    {
        $source = new SqlSource($this->adapter);
        $select = $source->select();
        $select->from(array('p' => 'product'), array())
                ->join(array('ppl' => 'product_pricelist'), new Expression('ppl.product_id = p.product_id and ppl.pricelist_id = 1'), array(), $select::JOIN_LEFT);

        $select->columns(array(
            'product_id' => new Expression('p.product_id'),
            'reference' => new Expression('p.reference'),
            'price' => new Expression('ppl.price'),
            'list_price' => new Expression('ppl.list_price'),
            'public_price' => new Expression('ppl.public_price'),
            'currency_reference' => new Expression("'CNY'")
        ));

        $store = new Store($source);
        $cm = $store->getColumnModel();


        $results = $cm->search()->regexp('/price/');
        $this->assertEquals(array('price', 'list_price', 'public_price'), $results->toArray());
        $formatterDb = \Soluble\FlexStore\Formatter::create('currency', array(
                    'currency_code' => new \Soluble\FlexStore\Formatter\RowColumn('currency_reference')
        ));
        $this->assertInstanceOf('Soluble\FlexStore\Formatter\RowColumn', $formatterDb->getCurrencyCode());
        $results->setFormatter($formatterDb);
        foreach ($results as $name) {
            $f = $cm->get($name)->getFormatter();
            $this->assertEquals($formatterDb, $f);
            $this->assertInstanceOf('Soluble\FlexStore\Formatter\RowColumn', $f->getCurrencyCode());
        }

        $formatterEur = \Soluble\FlexStore\Formatter::create('currency', array(
                    'currency_code' => 'EUR'
        ));

        $this->assertEquals('EUR', $formatterEur->getCurrencyCode());

        $cm->get('price')->setFormatter($formatterEur);
        
        $test = $cm->search()->in(array('price'))->toArray();
        $this->assertEquals(array('price'), $test);
        
        $cool = new Column('cool');
        $cm->add($cool);
        $test = $cm->search()->in(array('cool'))->toArray();
        $this->assertEquals(array('cool'), $test);

        
        $cool2 = new Column('cool2');
        $cm->add($cool2, 'cool');
        $test = $cm->search()->in(array('cool2'))->toArray();
        $this->assertEquals(array('cool2'), $test);
        
        $cm->sort(array('cool', 'cool2'));

        $cool3 = new Column('cool3');
        $cm->add($cool3);
        
        
        $test = $cm->search()->in(array('cool3'))->toArray();
        $this->assertEquals(array('cool3'), $test);
        
        
        $this->assertEquals($formatterEur, $cm->get('price')->getFormatter());
        $this->assertEquals($formatterDb, $cm->get('list_price')->getFormatter());
    }

    public function testSetFormatter()
    {
        $source = new SqlSource($this->adapter);
        $select = $source->select();
        $select->from(array('p' => 'product'), array())
                ->join(array('ppl' => 'product_pricelist'), new Expression('ppl.product_id = p.product_id and ppl.pricelist_id = 1'), array(), $select::JOIN_LEFT);

        $select->columns(array(
            'product_id' => new Expression('p.product_id'),
            'reference' => new Expression('p.reference'),
            'price' => new Expression('ppl.price'),
            'list_price' => new Expression('ppl.list_price'),
            'public_price' => new Expression('ppl.public_price'),
            'currency_reference' => new Expression("'CNY'")
        ));

        $store = new Store($source);
        $cm = $store->getColumnModel();

        $formatter = new CurrencyFormatter();
        $formatter->setLocale('fr_FR');
        $formatter->setCurrencyCode('EUR');

        $cm->get('price')->setFormatter($formatter);
        $data = $store->getData()->toArray();
        $this->assertEquals('10,20 €', $data[0]['price']);
        // Null will be transformed in 0,00 €
        $this->assertEquals('0,00 €', $data[3]['price']);

        $formatter->setLocale('en_US');
        $formatter->setCurrencyCode('USD');
        $cm->get('price')->setFormatter($formatter);
        $data = $store->getData()->toArray();
        $this->assertEquals('$10.20', $data[0]['price']);
        // Null will be transformed in 0,00 €
        $this->assertEquals('$0.00', $data[3]['price']);

        // store 2
        $store = new Store($source);

        $formatter = new CurrencyFormatter();
        $formatter->setLocale('fr_FR');
        $formatter->setCurrencyCode('EUR');
        $cm = $store->getColumnModel();
        $cm->setFormatter($formatter, array('price', 'list_price'));
        $data = $store->getData()->toArray();
        $this->assertEquals('10,20 €', $data[0]['price']);
        $this->assertEquals('15,30 €', $data[0]['list_price']);
    }

    public function testCustomColumn()
    {
        $select = new Select();
        $select->from(array('p' => 'product'), array())
                ->join(array('ppl' => 'product_pricelist'), new Expression('ppl.product_id = p.product_id and ppl.pricelist_id = 1'), array(), $select::JOIN_LEFT);

        $select->columns(array(
            'product_id' => new Expression('p.product_id'),
            'reference' => new Expression('p.reference'),
            'price' => new Expression('ppl.price'),
            'list_price' => new Expression('ppl.list_price'),
            'public_price' => new Expression('ppl.public_price')
        ));

        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();

        $cc = new Column('picture_url');
        $cc->setType('string');

        $cm->add($cc);
        $this->assertTrue($cm->get($cc->getName())->isVirtual());
        $cm->sort(array('picture_url', 'price', 'list_price'));
        $cm->exclude(array('reference'));

        $fct = function (\ArrayObject $row) {
            $row['picture_url'] = "http://" . $row['reference'];
        };
        $cm->addRowRenderer(new \Soluble\FlexStore\Renderer\ClosureRenderer($fct));

        $data = $source->getData()->toArray();
        $expected = array(
            'picture_url' => "http://TESTREF10",
            'price' => "10.200000",
            'list_price' => "15.300000",
            'product_id' => "10",
            'public_price' => "18.200000",
        );
        $this->assertEquals($expected, $data[0]);
    }

    public function testAddBeforeAndAfter()
    {
        $select = new Select();
        $select->from(array('p' => 'product'), array())
                ->join(array('ppl' => 'product_pricelist'), new Expression('ppl.product_id = p.product_id and ppl.pricelist_id = 1'), array(), $select::JOIN_LEFT);

        $select->columns(array(
            'product_id' => new Expression('p.product_id'),
            'reference' => new Expression('p.reference'),
            'price' => new Expression('ppl.price'),
            'list_price' => new Expression('ppl.list_price'),
            'public_price' => new Expression('ppl.public_price')
        ));

        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();

        $cc = new Column('test');
        $cc->setType(ColumnType::TYPE_STRING);

        $cm->add($cc);
        try {
            $cm->add($cc);
            $this->assertFalse(true, " should throw DuplicateColumnException");
        } catch (\Soluble\FlexStore\Column\Exception\DuplicateColumnException $ex) {
            $this->assertTrue(true);
        }
        
        // column must appear at the end
        $arr = array_keys((array) $cm->getColumns());
        $this->assertEquals('test', $arr[count($arr)-1]);
        
        
        // TEST INSERT AFTER
        $cc2 = new Column('insert_after');
        
        try {
            $cm->add($cc2, 'not_existentcolumn');
            $this->assertFalse(true, " should throw ColumnNotFoundException");
        } catch (\Soluble\FlexStore\Column\Exception\ColumnNotFoundException $ex) {
            $this->assertTrue(true);
        }
        
        $cm->add($cc2, 'product_id');

        // column must appear at the end
        $arr = array_keys((array) $cm->getColumns());
        $this->assertEquals('insert_after', $arr[1]);
        
        
        $cc2 = new Column('insert_after_end');
        $cm->add($cc2, 'test', ColumnModel::ADD_COLUMN_AFTER);
        $arr = array_keys((array) $cm->getColumns());
        $this->assertEquals('insert_after_end', $arr[count($arr)-1]);
         
         // TEST INSERT BEFORE
         $cc = new Column('insert_before');
        $cm->add($cc, 'product_id', ColumnModel::ADD_COLUMN_BEFORE);
        $arr = array_keys((array) $cm->getColumns());
        $this->assertEquals('insert_before', $arr[0]);
         
         
         // TEST MODE EXCEPTION
         $cc = new Column('invalid_mode');
        try {
            $cm->add($cc, 'product_id', 'invalid_mode');
            $this->assertFalse(true, " should throw InvalidArgumentException");
        } catch (\Soluble\FlexStore\Column\Exception\InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }
    }
    
    public function testSomeInvalidArgumentException()
    {
        $select = new Select();
        $select->from(array('p' => 'product'), array())
                ->join(array('ppl' => 'product_pricelist'), new Expression('ppl.product_id = p.product_id and ppl.pricelist_id = 1'), array(), $select::JOIN_LEFT);

        $select->columns(array(
            'product_id' => new Expression('p.product_id'),
            'reference' => new Expression('p.reference'),
            'price' => new Expression('ppl.price'),
            'list_price' => new Expression('ppl.list_price'),
            'public_price' => new Expression('ppl.public_price')
        ));

        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();

        try {
            $cm->exists("");
            $this->assertFalse(true, " should throw InvalidArgumentException");
        } catch (\Soluble\FlexStore\Column\Exception\InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }

        try {
            $cm->sort(array('product_id', 'undefined_col'));
            $this->assertFalse(true, " should throw InvalidArgumentException");
        } catch (\Soluble\FlexStore\Column\Exception\InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }

        try {
            $cm->exclude(new \stdClass());
            $this->assertFalse(true, " should throw InvalidArgumentException");
        } catch (\Soluble\FlexStore\Column\Exception\InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }

        try {
            $cm->includeOnly(new \stdClass());
            $this->assertFalse(true, " should throw InvalidArgumentException");
        } catch (\Soluble\FlexStore\Column\Exception\InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }

        try {
            $formatter = new \Soluble\FlexStore\Formatter\NumberFormatter();
            $cm->setFormatter($formatter, new \stdClass());
            $this->assertFalse(true, " should throw InvalidArgumentException");
        } catch (\Soluble\FlexStore\Column\Exception\InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }
    }

    public function testAddRowRenderer()
    {
        $select = new Select();
        $select->from(array('p' => 'product'), array())
                ->join(array('ppl' => 'product_pricelist'), new Expression('ppl.product_id = p.product_id and ppl.pricelist_id = 1'), array(), $select::JOIN_LEFT);

        $select->columns(array(
            'product_id' => new Expression('p.product_id'),
            'reference' => new Expression('p.reference'),
            'price' => new Expression('ppl.price'),
            'list_price' => new Expression('ppl.list_price'),
            'public_price' => new Expression('ppl.public_price')
        ));

        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();

        $fct = function (\ArrayObject $row) {
            $row['price'] = 200;
        };

        $fct2 = function (\ArrayObject $row) {
            if ($row['product_id'] == 113) {
                $row['reference'] = 'MyNEWREF';
            }
        };

        $cm->addRowRenderer(new \Soluble\FlexStore\Renderer\ClosureRenderer($fct));
        $cm->addRowRenderer(new \Soluble\FlexStore\Renderer\ClosureRenderer($fct2));

        $data = $source->getData()->toArray();
        foreach ($data as $row) {
            $this->assertEquals(200, $row['price']);
            if ($row['product_id'] == 113) {
                $this->assertEquals('MyNEWREF', $row['reference']);
            } else {
                $this->assertNotEquals('MyNEWREF', $row['reference']);
            }
        }
    }

    public function testGetColumns()
    {
        $columnModel = $this->columnModel;
        $this->assertInstanceOf('\Soluble\FlexStore\Column\ColumnModel', $columnModel);
        $columns = $columnModel->getColumns();
        $this->assertInstanceOf('ArrayObject', $columns);
        foreach ($columns as $key => $column) {
            $this->assertInstanceOf('Soluble\FlexStore\Column\Column', $column);
            $this->assertEquals($key, $column->getName());
        }
    }

    public function testExclusion()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('product');
        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();

        $excluded = array('product_id', 'legacy_mapping');
        $cm->exclude($excluded);
        $this->assertEquals($excluded, $cm->getExcluded());
    }

    public function testFindVirtual()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('product');
        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();

        $excluded = array('product_id', 'legacy_mapping');
        $cm->exclude($excluded);
        $cm->add(new Column('cool', $params = array('type' => 'string')));

        $virtual = $cm->search()->findVirtual()->toArray();
        $this->assertEquals(array('cool'), $virtual);
    }

    public function testSortColumns()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'email', 'username'));
        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();

        $sort = array('email', 'user_id');
        $cm->sort($sort);

        $this->assertEquals(array('email', 'user_id', 'password', 'username'), array_keys((array) $cm->getColumns()));
    }

    public function testSortColumnsThrowsDuplicateColumnException()
    {
        $this->setExpectedException('Soluble\FlexStore\Column\Exception\DuplicateColumnException');
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'email', 'username'));
        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();

        $sort = array('email', 'user_id', 'email', 'user_id');

        $cm->sort($sort);
    }

    public function testGetColumn()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'email', 'username'));
        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();
        $col = $cm->get('user_id');
        $this->assertInstanceOf('Soluble\FlexStore\Column\Column', $col);

        $select = new \Zend\Db\Sql\Select();
        $select->from('user');
        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();
        $col = $cm->get('email');
        $this->assertInstanceOf('Soluble\FlexStore\Column\Column', $col);
    }

    public function testHasColumn()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'username'));
        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();
        $this->assertTrue($cm->exists('user_id'));
        $this->assertTrue($cm->exists('password'));
        $this->assertFalse($cm->exists('email'));
    }

    public function testGetColumnThrowsColumnNotFoundException()
    {
        $this->setExpectedException('Soluble\FlexStore\Column\Exception\ColumnNotFoundException');
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'username'));

        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();
        $cm->get('this_column_not_exists');
    }

    public function testGetColumnThrowsInvalidArgumentException()
    {
        $this->setExpectedException('Soluble\FlexStore\Column\Exception\InvalidArgumentException');
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'username'));
        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();
        $cm->get(new \stdClass());
    }

    public function testHasColumnThrowsInvalidArgumentException()
    {
        $this->setExpectedException('Soluble\FlexStore\Column\Exception\InvalidArgumentException');
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'username'));
        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();
        $cm->exists(new \stdClass());
    }

    public function testIncludeOnly()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'email', 'displayName', 'username', 'password'));
        $source = new SqlSource($this->adapter, $select);
        $cm = $source->getColumnModel();

        $include_only = array('email', 'user_id');

        $cm->includeOnly($include_only);
        $this->assertEquals($include_only, array_keys((array) $cm->getColumns()));
    }

    public function testExclusionRetrieval()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'email', 'displayname', 'username', 'password'));

        $source = new SqlSource($this->adapter, $select);

        $excluded = array('user_id', 'email');
        $cm = $source->getColumnModel();
        $cm->exclude($excluded);
        $this->assertEquals($excluded, $cm->getExcluded());

        $data = $source->getData();
        $this->isInstanceOf('Soluble\FlexStore\ResultSet\ResultSet');

        $d = $data->toArray();
        $first = array_keys($d[0]);

        $this->assertEquals(3, count($first));
        $this->assertEquals('displayname', array_shift($first));
        $this->assertEquals('username', array_shift($first));
    }
}
