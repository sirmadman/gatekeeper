<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Collection\Mysql;

class PermissionCollection extends Mysql
{
    /**
     * Find the permissions for the given group ID
     *
     * @param integer $groupId Group ID
     *
     * @return void
     */
    public function findByGroupId(int $groupId): void
    {
        $prefix = $this->getPrefix();
        $data = array('groupId' => $groupId);
        $sql = 'select p.* from ' . $prefix . 'permissions p, ' . $prefix . 'group_permission gp'
                . ' where p.id = gp.permission_id'
                . ' and gp.group_id = :groupId';

        $results = $this->getDb()->fetch($sql, $data);

        if ($results !== false) {
            foreach ($results as $result) {
                $perm = new PermissionModel($this->getDb(), $result);
                $this->add($perm);
            }
        }
    }

    /**
     * Find child permission by the parent permission ID
     *
     * @param integer $permId Permission ID
     *
     * @return void
     */
    public function findChildrenByPermissionId($permId): void
    {
        $prefix = $this->getPrefix();
        $data = array('permId' => $permId);
        $sql = 'select p.* from ' . $prefix . 'permissions p, ' . $prefix . 'permission_parent pp'
                . ' where p.id = pp.permission_id'
                . ' and p.parent_id = :permId';

        $results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $group = new PermissionModel($this->getDb(), $result);
            $this->add($group);
        }
    }
}
