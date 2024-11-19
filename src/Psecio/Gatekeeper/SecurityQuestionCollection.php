<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Collection\Mysql;

class SecurityQuestionCollection extends Mysql
{
    /**
    * Find the security questions for the given user ID
    *
    * @param integer $userId User ID
    *
    * @return boolean Result
    */
    public function findByUserId(int $userId): bool
    {
        $data = array('userId' => $userId);
        $sql = 'select * from ' . $this->getPrefix() . 'security_questions where user_id = :userId';

        $results = $this->getDb()->fetch($sql, $data);
        if ($results === false) {
            return false;
        }

        foreach ($results as $result) {
            $question = new SecurityQuestionModel($this->getDb(), $result);
            $this->add($question);
        }
        return true;
    }
}
