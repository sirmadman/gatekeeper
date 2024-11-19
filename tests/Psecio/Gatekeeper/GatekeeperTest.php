<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Base;
use Psecio\Gatekeeper\DataSource\Stub;
use Psecio\Gatekeeper\Restrict\Ip;
use InvalidArgumentException;

class GatekeeperTest extends Base
{
    public function setUp(): void
    {
        // $config = array('test' => 1);
        // Gatekeeper::init(null, $config);
    }
    public function tearDown(): void
    {

    }

    /**
     * Test the getter/setter for datasources
     */
    public function testGetSetDatasource()
    {
        $ds = $this->getMockBuilder(Stub::class)
            ->disableOriginalConstructor()
            ->onlyMethods(array('find', 'delete'))
            ->getMock();

        Gatekeeper::setDatasource($ds);
        $this->assertEquals(Gatekeeper::getDatasource(), $ds);
    }

    /**
     * Test the enable/disable of throttling
     */
    public function testEnableDisableThrottle()
    {
        Gatekeeper::disableThrottle();
        $this->assertFalse(Gatekeeper::throttleStatus());

        Gatekeeper::enableThrottle();
        $this->assertTrue(Gatekeeper::throttleStatus());
    }

    /**
     * Test getting the user's throttle information (model instance)
     */
    // public function testGetUserThrottle()
    // {
    //     $userId = 42;

    //     // This is our model that will be returned
    //     $ds = $this->buildMock(null);
    //     $throttle1 = new ThrottleModel($ds, array('userId' => $userId));

    //     $ds = $this->buildMock($throttle1, 'find');
    //     $throttle = new ThrottleModel($ds);

    //     $gk = $this->getMockBuilder('\Psecio\Gatekeeper\Gatekeeper')
    //         ->onlyMethods(array('findThrottleByUserId'))
    //         ->getMock();

    //     $config = array('name' => 'test');
    //     $gk::init(null, $config, $ds);

    //     $gk->method('findThrottleByUserId')
    //         ->willReturn($throttle);

    //     $result = $gk::getUserThrottle($userId);
    //     $this->assertEquals(42, $result->userId);
    // }

    /**
     * Test that a restriction is correctly made
     */
    public function testCreateRestriction()
    {
        Gatekeeper::restrict('ip', array());
        $restrict = Gatekeeper::getRestrictions();
        $this->assertCount(1, $restrict);
        $this->assertTrue($restrict[0] instanceof Ip);
    }

    /**
     * Test the creation of an invalid (not found) restriction
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalidRestriction()
    {
        $this->expectException(InvalidArgumentException::class);
        Gatekeeper::restrict('foobar', array());
    }

    /**
     * Test the hash equality checking
     */
    public function testHashEqualsValid()
    {
        $hash = hash('sha256', mt_rand());
        $this->assertTrue(hash_equals($hash, $hash));
    }

    /**
     * Test that false is returned when the hashes are different lengths
     */
    public function testHashEqualsDifferentLength()
    {
        $hash = sha1(mt_rand());
        $this->assertFalse(hash_equals($hash, md5(mt_rand())));
    }
}