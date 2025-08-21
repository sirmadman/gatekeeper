<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Base;
use Psecio\Gatekeeper\DataSource;

class DataSourceTest extends Base
{
    /**
     * Test the getter/setter for the data source configuration
     */
    public function testGetSetConfigFunction()
    {
        $config = array('test' => 'foo');
        $ds = $this->getMockForAbstractClass(DataSource::class, array($config));

        $ds->setConfig($config);
        $this->assertEquals($ds->getConfig(), $config);
    }

    /**
     * Test the setting for the data source configuration in constructor
     */
    public function testGetSetConfigConstruct()
    {
        $config = array('test' => 'foo');
        $ds = $this->getMockForAbstractClass(DataSource::class, array($config));
        $this->assertEquals($ds->getConfig(), $config);
    }
}
