<?php

namespace Soluble\Normalist\Synthetic;

use Soluble\Db\Metadata\Source;
use Soluble\Db\Metadata\Exception;

use Zend\Db\Sql\Where;
use Zend\Db\Sql\Having;
use Zend\Db\Sql\Predicate;
use Zend\Db\Sql\Expression;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-01 at 12:55:35.
 */
class TableSearchTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var TableManager
     */
    protected $tableManager;


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
        $adapter = \SolubleTestFactories::getDbAdapter();
        $cache   = \SolubleTestFactories::getCacheStorage();
        //$metadata = new Source\MysqlISMetadata($adapter);
       // $metadata = new Source\Mysql\InformationSchema($adapter);
        //$metadata->setCache($cache);

        //$this->tableManager = new TableManager($adapter);
        //$this->tableManager->setMetadata($metadata);
        $this->tableManager = \SolubleTestFactories::getTableManager();
        

        $this->table = $this->tableManager->table('product_category');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
        unset($this->tableManager);
        unset($this->table);        
    }
    

    public function testLimit()
    {
        $results = $this->table->search()->limit(10)->toArray();
        $this->assertEquals(10, count($results));
    }
    public function testExecute() {
        $rs = $this->table->search()->limit(10)->execute();
        $this->assertInstanceOf('Soluble\Normalist\Synthetic\ResultSet\ResultSet', $rs);
    }

    public function testOffset()
    {
        $rs1 = $this->table->search()->limit(10)->toArray();
        $this->assertEquals(10, count($rs1));
        
        $rs2 = $this->table->search()->limit(10)->offset(1)->toArray();
        $this->assertEquals(10, count($rs2));
        
        $this->assertEquals($rs1[1], $rs2[0]);
    }
    
    

    public function testColumns()
    {
        
        $results = $this->table->search()->columns(array('reference'))->limit(1)->toArray();
        $keys = array_keys($results[0]);
        $this->assertEquals(1, count($keys));
        $this->assertEquals('reference', $keys[0]);
    }
    
    public function testColumnsAliases()
    {
        
        $results = $this->table->search()->columns(array('aliased_reference' => 'reference'))->limit(1)->toArray();
        $keys = array_keys($results[0]);
        $this->assertEquals(1, count($keys));
        $this->assertEquals('aliased_reference', $keys[0]);
    }
    
    
    public function testIncompletColumnsThrowsLogicException()
    {
        $this->setExpectedException('Soluble\Normalist\Synthetic\Exception\LogicException');
        $rs = $this->table->search()->columns(array('reference'))->limit(1)->execute();
        
        
        foreach($rs as $record) {
           $this->assertFalse(true, "This part of the code should never be reached");
        }
    }
    


    public function testOrder()
    {
        $rs1 = $this->table->search()
                        ->order(array('reference DESC'))
                        ->toArray();

        $rs2 = $this->table->search()
                        ->order(array('reference ASC'))
                        ->toArray();
        
        
        $this->assertInternalType('array', $rs1);
        $this->assertInternalType('array', $rs2);
        
        $firstrs1 = $rs1[0]['reference'];
        $lastrs2 = $rs2[count($rs2)-1]['reference'];
        
        $this->assertEquals($firstrs1, $lastrs2);
        
        
    }


    public function testWhere()
    {
        // Simple where
        $results = $this->table->search()
                        ->where(array('reference' => 'AC'))
                        ->order(array('reference DESC', 'category_id ASC'))
                        ->toArray();
        $this->assertInternalType('array', $results);
        $this->assertEquals(1, count($results));
        $this->assertEquals('AC', $results[0]['reference']);
        
        // Simple with constant
        $results = $this->table->search()
                        ->where('category_id = 12')
                        ->toArray();
        $this->assertEquals(1, count($results));
        $this->assertEquals('AC', $results[0]['reference']);
        
        // Advanced with testing null
        $results = $this->table->search()
                        ->where(array(
                                 'category_id' => 12,
                                 'updated_by' => null ))
                        ->toArray();
        $this->assertEquals('AC', $results[0]['reference']);
        

        $results = $this->table->search()
                        ->where(array(
                                 'category_id' => 12,
                                 'root' => null ))
                        ->toArray();
        $this->assertEquals(0, count($results));
        
        
        // Advanced with getting non null
        
        $results = $this->table->search()
                        ->where(array(
                                 'category_id' => 12,
                                 new Predicate\IsNotNull('root') 
                                ))
                        ->toArray();
        
        $this->assertEquals('AC', $results[0]['reference']);

        // Advanced with predicate IN
        
        $results = $this->table->search()
                        ->where(array(
                                 new Predicate\In('category_id', array(12, 10)) 
                                ))
                        ->order('category_id DESC')            
                        ->toArray();
        $this->assertEquals(2, count($results));
        $this->assertEquals(12, $results[0]['category_id']);
        $this->assertEquals(10, $results[1]['category_id']);
        

        // Advanced with operator constant
        $results = $this->table->search()
                ->where('category_id < 10')
                ->toArray();
        $test_min = true;
        foreach($results as $row) {
            if ($row['category_id'] > 9) {
                $test_min = false;
            }
        }
        $this->assertTrue($test_min);

        // advanced with range
        $results = $this->table->search()
                ->where('category_id < 10')
                ->where('category_id > 5')
                ->toArray();
        $test_min = true;
        $test_max = true;
        foreach($results as $row) {
            if ($row['category_id'] > 9) {
                $test_min = false;
            }
            if ($row['category_id'] < 5) {
                $test_max = false;
            }
        }
        $this->assertTrue($test_min);
        $this->assertTrue($test_max);
        
        // Advanced OR
        $results = $this->table->search()
                 ->where(array(
                            'reference' => 'AC',
                            'legacy_mapping' =>  'GT'
                          ),
                          Predicate\PredicateSet::OP_OR
                        )
                 ->order('reference ASC')
                ->toArray();
        $this->assertEquals(2, count($results));
        $this->assertEquals('AC', $results[0]['reference']);
        $this->assertEquals('GT', $results[1]['reference']);

        // Advanced OR version 2
        $results = $this->table->search()
                 ->where(array(
                            "reference =   'AC'",
                            "reference = 'GT'"
                          ),
                          Predicate\PredicateSet::OP_OR
                        )
                 ->order('reference ASC')
                ->toArray();
        
        $this->assertEquals(2, count($results));
        $this->assertEquals('AC', $results[0]['reference']);
        $this->assertEquals('GT', $results[1]['reference']);
        
        // Advanced where with closure
        $results = $this->table->search()
                        ->where(function (Where $where) {
                                   $where->like('reference', 'AC%');
                                })
                         ->columns(array('reference'))
                         ->limit(100)               
                        ->toArray();
                                
        $test_start = true;                        
        foreach ($results as $row) {
            if (!preg_match('/^AC/', $row['reference'])) {
                $test_start = false;
            }
        }
        $this->assertTrue($test_start);
        
    }
    
    public function testOrWhere() {
        
        $results = $this->table->search()
                 ->orWhere(array(
                            'reference' => 'AC',
                            'legacy_mapping' =>  'GT'
                          )
                        )
                 ->order('reference ASC')
                ->toArray();
        $this->assertEquals(2, count($results));
        $this->assertEquals('AC', $results[0]['reference']);
        $this->assertEquals('GT', $results[1]['reference']);
        
    }
    
    public function testWhereWithClosure()
    {
        $search = $this->tableManager->table('user')->search();        
        
        $results = $search->where(function (Where $where) {

            $where->like('email', '%@example.com');

            $where->in('country', array('FR', 'US'))
                  ->between('birth_date', 1970, 2001);

            $where->lessThan('birth_date', 1980)
                  ->and
                  ->greaterThan('birth_date', 2010);

            $where->isNotNull('zipcode');

            $where->or->nest
                       ->equalTo('name', 'Bill')
                       ->or->like('last_name', '%Gates%');
            
            $where->like('first_name', "%;'DROP DATABASE' `DROP TABLE`");
        });
        
        $expected = <<<EOF
            SELECT `user`.* FROM `user` WHERE `email` LIKE '%@example.com' AND `country` IN ('FR', 'US') AND `birth_date` BETWEEN '1970' AND '2001' AND `birth_date` < '1980' AND `birth_date` > '2010' AND `zipcode` IS NOT NULL OR (`name` = 'Bill' OR `last_name` LIKE '%Gates%') AND `first_name` LIKE '%;\'DROP DATABASE\' `DROP TABLE`'                
EOF;
        
        $this->assertEquals(trim($expected), trim($search->getSql()));
        
    }

    public function testJoin()
    {
        
        $tm = $this->tableManager;
        $search = $tm->table('user')->search('u');        
        
        $results = $search
            ->join(array('c' => 'country'), 'u.country_id = c.country_id')
            ->where(function (Where $where) {
                $where->like('u.email', '%@example.com');
                $where->like('c.name', 'Unite%');
            });
                
        
        $sql = $search->getSql();
        
        $expected = <<<EOF
            SELECT `u`.* FROM `user` AS `u` INNER JOIN `country` AS `c` ON `u`.`country_id` = `c`.`country_id` WHERE `u`.`email` LIKE '%@example.com' AND `c`.`name` LIKE 'Unite%'
EOF;
        
        $this->assertEquals(trim($expected), trim($sql));
        
        
    }        
    

    public function testJoinRight()
    {
        
        $tm = $this->tableManager;
        $search = $tm->table('user')->search('u');        
        
        $results = $search
            ->joinRight(array('c' => 'country'), 'u.country_id = c.country_id')
            ->where(function (Where $where) {
                $where->like('u.email', '%@example.com');
                $where->like('c.name', 'Unite%');
            });
                
        
        $sql = $search->getSql();
        
        $expected = <<<EOF
            SELECT `u`.* FROM `user` AS `u` RIGHT JOIN `country` AS `c` ON `u`.`country_id` = `c`.`country_id` WHERE `u`.`email` LIKE '%@example.com' AND `c`.`name` LIKE 'Unite%'
EOF;
        
        $this->assertEquals(trim($expected), trim($sql));
        
        
    }        
    
    public function testJoinLeftWithoutAlias()
    {
        $tm = $this->tableManager;
        $search = $tm->table('product_category')->search();        
        
        
        $search
            ->joinLeft("product_category_translation", "product_category_translation.category_id = product_category.category_id")
            ->where(function (Where $where) {
                $where->nest->equalTo('product_category_translation.lang', 'it')->or->isNull('product_category_translation.lang')->unnest;
            })
            ->prefixedColumns(array(
                    'product_category.category_id',
                    'title' => 'product_category.title', 
                    'translated_title' => 'product_category_translation.title', 
                    'auto_title' => new Expression('COALESCE(product_category_translation.title, product_category.title)')
                ))->limit(10);                    
                
        
        
        $results = $search->execute();
        $array = $results->toArray();
        $this->assertEquals('ROOT', $array[0]['title']);

        $this->setExpectedException('Soluble\Normalist\Synthetic\Exception\LogicException');
        $results->current();
    }
    
    
    public function testJoinLeft()
    {
        $tm = $this->tableManager;
        $search = $tm->table('product_category')->search('pc');        
        
        
        $search
            ->joinLeft(array('pc18' => "product_category_translation"), "pc18.category_id = pc.category_id")
            ->where(function (Where $where) {
                $where->nest->equalTo('pc18.lang', 'it')->or->isNull('pc18.lang')->unnest;
            })
            ->prefixedColumns(array(
                    'pc.category_id',
                    'title' => 'pc.title', 
                    'translated_title' => 'pc18.title', 
                    'auto_title' => new Expression('COALESCE(pc18.title, pc.title)')
                ))->limit(10);                    
                
        
        
        $results = $search->execute();
        $array = $results->toArray();
        $this->assertEquals('ROOT', $array[0]['title']);

        $this->setExpectedException('Soluble\Normalist\Synthetic\Exception\LogicException');
        $results->current();
    }
    
    public function testGroup()
    {
        $prefix = 'wp_';
        $tm = $this->tableManager;
        $search = $tm->table("{$prefix}posts")->search('p');        
        
        
        $search
            ->joinLeft(
                    array('c' => "{$prefix}comments"), "c.comment_post_ID = p.ID")
            ->group(array('post_id', 'post_title'))
            ->where(function (Where $where) {
                $where->equalTo('post_status', 'publish');
            })                
            ->having(function(Having $having) {
                $having->greaterThanOrEqualTo('count_comment', 1);
             })
            ->order(array(
                'count_comment DESC',
                'p.post_date DESC')
            ) 
            ->prefixedColumns(array(
                    'post_id'       => 'p.ID',
                    'post_title'    => 'p.post_title',
                    'count_comment' => new Expression('COUNT(c.comment_ID)') 
                ));
             
        $results = $search->execute();
        $array = $results->toArray();
        $this->assertEquals(1, $array[0]['post_id']);

        $this->setExpectedException('Soluble\Normalist\Synthetic\Exception\LogicException');
        $results->current();
        
        
    }
    

    public function testGetSelect()
    {
        $select = $this->table->search()->getSelect();
        $this->assertInstanceOf('Soluble\Db\Sql\Select', $select);
    }

    public function testGetSql()
    {
        $sql = $this->table->search()->getSql();
        $this->assertInternalType('string', $sql);
        $this->assertContains('SELECT', $sql);
    }

    public function testToJson()
    {
        $results = $this->table->search()->limit(10)->toJson();
        $this->assertInternalType('string', $results);
        $decoded = json_decode($results, $assoc=true);
        
        $results = $this->table->search()->limit(10)->toArray();
        $this->assertEquals($results, $decoded);
        
    }

    public function testToArray()
    {
        $results = $this->table->search()->toArray();
        $this->assertInternalType('array', $results);
    }

    public function testToArrayColumn()
    {
        $results = $this->table->search()
                        ->where(array('reference' => 'AC'))
                        ->order(array('reference DESC', 'category_id ASC'))
                        ->toArrayColumn('category_id', 'reference');
        $this->assertInternalType('string', $results['AC']);
        $this->assertArrayHasKey('AC', $results);
        $this->assertEquals(12, $results['AC']);        
    }
    
}
