<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Collection\Mysql;

class UserCollection extends Mysql
{
    /**
     * Find the users belonging to the given group
     *
     * @param integer $groupId Group ID
     *
     * @return boolean Result
     */
    public function findByGroupId(int $groupId): bool
    {
        $prefix = $this->getPrefix();
        $data = array('groupId' => $groupId);
        $sql = 'select u.* from ' . $prefix . 'users u, ' . $prefix . 'user_group ug'
            . ' where ug.group_id = :groupId'
            . ' and ug.user_id = u.id';

        $results = $this->getDb()->fetch($sql, $data);
        if ($results === false) {
            return false;
        }

        foreach ($results as $result) {
            $user = new UserModel($this->getDb(), $result);
            $this->add($user);
        }
        return true;
    }

    /**
     * Find the users that have a permission defined by the
     *     given ID
     *
     * @param integer $permId Permission ID
     */
    public function findUsersByPermissionId(int $permId): bool
    {
        $prefix = $this->getPrefix();
        $data = array('permId' => $permId);
        $sql = 'select u.* from ' . $prefix . 'users u, ' . $prefix . 'user_permission up'
            . ' where up.permission_id = :permId'
            . ' and up.user_id = u.id';

        $results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $user = new UserModel($this->getDb(), $result);
            $this->add($user);
        }
        return true;
    }
}
