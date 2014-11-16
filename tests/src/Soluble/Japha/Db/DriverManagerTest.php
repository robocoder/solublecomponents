<?php

namespace Soluble\Japha\Db;

use Soluble\Japha\Bridge\PhpJavaBridge as Pjb;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-16 at 18:01:31.
 */
class DriverManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DriverManager
     */
    protected $driverManager;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->driverManager = new DriverManager;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    public function testGetDriverManager()
    {
        $dm = $this->driverManager->getDriverManager();
        $this->assertInstanceOf('Soluble\Japha\Bridge\JavaObjectInterface', $dm);
        $className = Pjb::getDriver()->getClassName($dm);
        $this->assertEquals('java.sql.DriverManager', $className);
    }
    
    
    public function testCreateConnection()
    {
        //$this->driverManager->createConnection()
        $config = \SolubleTestFactories::getDatabaseConfig();
        $host = $config['hostname'];
        $db = $config['database'];
        $user = $config['username'];
        $password = $config["password"];
        
        $dsn = "jdbc:mysql://$host/$db?user=$user&password=$password";
        
        $conn = $this->driverManager->createConnection($dsn);
        $className = Pjb::getDriver()->getClassName($conn);
        $this->assertEquals('com.mysql.jdbc.JDBC4Connection', $className);
    }


}