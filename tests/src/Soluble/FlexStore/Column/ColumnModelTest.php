<?php

namespace Soluble\FlexStore\Column;

use Soluble\FlexStore\Source\Zend\SelectSource;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

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
            'select' => $select
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

        /*
          new SelectSource($params);

          $store = new FlexStore($select);
          $store->setAdapter($adapter);

          $cm = $store->setColumnModel();
          $cm->setTranslate($translate)

         */
        /**
          $store->setSource($selectSource);
          $cm = $store->getSource()->getColumnModel();



          $cm = $store->getSource()->getColumnModel();
          $cm->setTranslate();
          $cm->getColumns();


          $store->getData();

          ->from(array('p' => 'product'));
          $store->select()

          $store = new Store('zend\db', $params);

          $cm = $store->getColumnModel();
          $moneyRenderer = new MoneyRenderer();
          $cm->get('col1')->setHeader('cool');
          $cm->find(['price', 'list_price'])->setRenderer($moneyRenderer);
          $cm->findByRegExp('/^price$/')->setRenderer();
          $cm->all()->translateHeader($translate);
          $cm->all()->translateDescription($translate);


          $cm->setIncludeOnly(array('col1', 'col2'));
          $cm->getColumn('col1')->setRenderer()
          ->setHeader()
          ->setNumber()
          ->setSortable()
          ->setExcluded()
          ->setHidden()
          ->setGroupable();

         */
        $params = array(
            'adapter' => $this->adapter,
            'select' => $select
        );
        $source = new SelectSource($params);
        $cm = $source->getColumnModel();

        /*
          $cm->setRenderer(array('column', 'column2'), $my_renderer);
          $cm->setRenderer('column3', $my_renderer2);

          $renderer->apply($column, $data) {
          $data
          } */

        $fct = function(\ArrayObject $row) {
            $row['price'] = 200;
        };

        $fct2 = function(\ArrayObject $row) {
            if ($row['product_id'] == 113) {
                $row['reference'] = 'MyNEWREF';
            }
        };


        // $resultset = $source->getData();
        //$resultset->setHydratedColumns($columns);
        //$resultset

        $cm->addRowRenderer($fct);
        $cm->addRowRenderer($fct2);

        $data = $source->getData()->toArray();
        foreach ($data as $row) {
            $this->assertEquals(200, $row['price']);
            if ($row['product_id'] == 113) {
                $this->assertEquals('MyNEWREF', $row['reference']);
            } else {
                $this->assertNotEquals('MyNEWREF', $row['reference']);
            }
        }
        //die();
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
        $params = array(
            'adapter' => $this->adapter,
            'select' => $select
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
            'select' => $select
        );
        $source = new SelectSource($params);
        $cm = $source->getColumnModel();

        $sort = array('email', 'user_id');
        $cm->sortColumns($sort);

        $this->assertEquals(array('email', 'user_id', 'password', 'username'), array_keys((array) $cm->getColumns()));
    }

    public function testSortColumnsThrowsDuplicateColumnException()
    {
        $this->setExpectedException('Soluble\FlexStore\Column\Exception\DuplicateColumnException');
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'email', 'username'));
        $params = array(
            'adapter' => $this->adapter,
            'select' => $select
        );
        $source = new SelectSource($params);
        $cm = $source->getColumnModel();

        $sort = array('email', 'user_id', 'email', 'user_id');

        $cm->sortColumns($sort);
    }

    public function testGetColumn()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'email', 'username'));
        $params = array(
            'adapter' => $this->adapter,
            'select' => $select
        );
        $source = new SelectSource($params);
        $cm = $source->getColumnModel();
        $col = $cm->getColumn('user_id');
        $this->assertInstanceOf('Soluble\FlexStore\Column\Column', $col);

        $select = new \Zend\Db\Sql\Select();
        $select->from('user');
        $params = array(
            'adapter' => $this->adapter,
            'select' => $select
        );
        $source = new SelectSource($params);
        $cm = $source->getColumnModel();
        $col = $cm->getColumn('email');
        $this->assertInstanceOf('Soluble\FlexStore\Column\Column', $col);
    }

    public function testHasColumn()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'username'));
        $params = array(
            'adapter' => $this->adapter,
            'select' => $select
        );
        $source = new SelectSource($params);
        $cm = $source->getColumnModel();
        $this->assertTrue($cm->hasColumn('user_id'));
        $this->assertTrue($cm->hasColumn('password'));
        $this->assertFalse($cm->hasColumn('email'));
    }

    public function testGetColumnThrowsColumnNotFoundException()
    {
        $this->setExpectedException('Soluble\FlexStore\Column\Exception\ColumnNotFoundException');
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'username'));
        $params = array(
            'adapter' => $this->adapter,
            'select' => $select
        );
        $source = new SelectSource($params);
        $cm = $source->getColumnModel();
        $cm->getColumn('this_column_not_exists');
    }

    public function testGetColumnThrowsInvalidArgumentException()
    {
        $this->setExpectedException('Soluble\FlexStore\Column\Exception\InvalidArgumentException');
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'username'));
        $params = array(
            'adapter' => $this->adapter,
            'select' => $select
        );
        $source = new SelectSource($params);
        $cm = $source->getColumnModel();
        $cm->getColumn(new \stdClass());
    }

    public function testHasColumnThrowsInvalidArgumentException()
    {
        $this->setExpectedException('Soluble\FlexStore\Column\Exception\InvalidArgumentException');
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'password', 'username'));
        $params = array(
            'adapter' => $this->adapter,
            'select' => $select
        );
        $source = new SelectSource($params);
        $cm = $source->getColumnModel();
        $cm->hasColumn(new \stdClass());
    }

    public function testIncludeOnly()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'email', 'displayName', 'username', 'password'));
        $params = array(
            'adapter' => $this->adapter,
            'select' => $select
        );
        $source = new SelectSource($params);
        $cm = $source->getColumnModel();

        $include_only = array('email', 'user_id');

        $cm->setIncludeOnly($include_only);
        $this->assertEquals($include_only, array_keys((array) $cm->getColumns()));
    }

    public function testExclusionRetrieval()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('user')->columns(array('user_id', 'email', 'displayname', 'username', 'password'));
        $params = array(
            'adapter' => $this->adapter,
            'select' => $select
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
            'select' => $select
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
