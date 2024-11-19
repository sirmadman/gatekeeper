<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Collection\Mysql;

class AuthTokenCollection extends Mysql
{
    /**
    * Find the current token records for the provided user ID
    *
    * @param integer $userId User ID
    *
    * @return boolean Result
    */
    public function findTokensByUserId(int $userId): bool
    {
        $sql = 'select * from auth_tokens where user_id = :userId';
        $data = [ 'userId' => $userId];

        $results = $this->getDb()->fetch($sql, $data);
        if ($results === false) {
            return false;
        }
        foreach ($results as $result) {
            $token = new AuthTokenModel($this->getDb(), $result);
            $this->add($token);
        }
        return true;
    }
}
