<?php

namespace Psecio\Gatekeeper\DataSource;

include_once __DIR__.'/../MockPdo.php';
include_once __DIR__.'/../MockModel.php';

use Psecio\Gatekeeper\Base;
use Psecio\Gatekeeper\MockPdo;
use Psecio\Gatekeeper\MockModel;
use Psecio\Gatekeeper\DataSource\Mysql;

class MysqlTest extends Base
{
    public function testCreatePdoOnConstruct()
    {
        $config = array(
            'username' => 'foo',
            'password' => 'bar',
            'name' => 'dbname',
            'host' => '127.0.0.1'
        );
        $pdo = $this->getMockBuilder(MockPdo::class)->getMock();

        $mysql = $this->getMockBuilder(Mysql::class)
            ->setConstructorArgs(array($config, $pdo))
            ->onlyMethods(array('buildPdo'))
            ->getMock();

        $this->assertEquals($mysql->getDb(), $pdo);
    }

    /**
     * Test the getter/setter of the DB instance
     *     (just uses a basic object)
     */
    public function testGetSetDatabaseInstance()
    {
        $mysql = $this->getMockBuilder(Mysql::class)
            ->disableOriginalConstructor()
            ->onlyMethods(array('buildPdo'))
            ->getMock();

        $db = (object)array('test' => 'foo');
        $mysql->setDb($db);

        $this->assertEquals($mysql->getDb(), $db);
    }

    /**
     * Test getting the table name for the model instance
     */
    public function testGetTableName()
    {
        $config = array();
        $pdo = $this->getMockBuilder(MockPdo::class)->getMock();

        $ds = $this->getMockBuilder(Mysql::class)
            ->setConstructorArgs(array($config, $pdo))
            ->onlyMethods(array('buildPdo'))
            ->getMock();

        $mysql = new MockModel($ds);
        $this->assertEquals('test', $mysql->getTableName());
    }
}
