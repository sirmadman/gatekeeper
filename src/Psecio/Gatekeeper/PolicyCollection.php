<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Collection\Mysql;

class PolicyCollection extends Mysql
{
    /**
    * Get the current list of policies
    *
    * @param integer $limit Limit the number of records
    *
    * @return boolean Result
    */
    public function getList(?int $limit = null): bool
    {
        $sql = 'select * from policies';
        if ($limit !== null) {
            $sql .= ' limit ' . $limit;
        }
        $results = $this->getDb()->fetch($sql);
        if ($results === false) {
            return false;
        }

        foreach ($results as $result) {
            $policy = new PolicyModel($this->getDb(), $result);
            $this->add($policy);
        }
        return true;
    }
}
