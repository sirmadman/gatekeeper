<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Base;
use Psecio\Gatekeeper\DataSource\Mysql;
use Psecio\Gatekeeper\UserGroupCollection;

class UserGroupCollectionTest extends Base
{
    /**
     * Test the location of groups a member is a part of
     */
    public function testFindGroupsByUserId()
    {
        $userId = 1;
        $return = array(
            array('name' => 'group1', 'description' => 'Group #1'),
            array('name' => 'group2', 'description' => 'Group #2')
        );

        $ds = $this->buildMock($return, 'fetch');
        $groups = new UserGroupCollection($ds);

        $groups->findByUserId($userId);
        $this->assertCount(2, $groups);
    }

    /**
     * Test the creation of new collection items based on data given
     */
    public function testCreateRecordsFromModelDataById()
    {
        $ds = $this->getMockBuilder(Mysql::class)
            ->disableOriginalConstructor()
            ->onlyMethods(array('save', 'fetch'))
            ->getMock();

        $ds->method('save')->willReturn(true);

        $userModel = new UserModel($ds, array('id' => 1));
        $data = array(array('id' => 1, 'name' => 'Group #1'));
        $ds->method('fetch')->willReturn($data);

        $groupIdList = array(1, 2, 3);

        $groups = new UserGroupCollection($ds);
        $groups->create($userModel, $groupIdList);

        $this->assertEquals(
            count($groups->toArray()), count($groupIdList)
        );
    }

    /**
     * Test the creation of new collection items based on data given
     */
    public function testCreateRecordsFromModelDataByName()
    {
        $ds = $this->getMockBuilder(Mysql::class)
            ->disableOriginalConstructor()
            ->onlyMethods(array('save', 'fetch'))
            ->getMock();

        $ds->method('save')->willReturn(true);

        $userModel = new UserModel($ds, array('id' => 1));
        $data = array(array('id' => 1, 'name' => 'Group #1'));
        $ds->method('fetch')->willReturn($data);

        $groupNameList = array('group1', 'group2', 'group3');

        $groups = new UserGroupCollection($ds);
        $groups->create($userModel, $groupNameList);

        $this->assertEquals(
            count($groups->toArray()), count($groupNameList)
        );
    }
}