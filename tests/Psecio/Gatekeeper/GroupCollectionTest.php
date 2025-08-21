<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Base;
use Psecio\Gatekeeper\GroupCollection;
use Psecio\Gatekeeper\GroupModel;

class GroupCollectionTest extends Base
{
    /**
     * Test the location and conversion of child groups into instances
     */
    public function testFindChildrenGroups()
    {
        $groupId = 1;
        $return = array(
            array('name' => 'group1', 'description' => 'Group #1'),
            array('name' => 'group2', 'description' => 'Group #2')
        );

        $ds = $this->buildMock($return, 'fetch');
        $groups = new GroupCollection($ds);

        $groups->findChildrenByGroupId($groupId);
        $this->assertCount(2, $groups->toArray());

        $groups = $groups->toArray();
        $this->assertTrue($groups[0] instanceof GroupModel);
    }
}
