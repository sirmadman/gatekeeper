<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Model\Mysql;

/**
* SecurityQuestion class
*
* @property string $id
* @property string $question
* @property string $answer
* @property string $userId
* @property string $created
* @property string $updated
*/
class SecurityQuestionModel extends Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected string $tableName = 'security_questions';

    /**
     * Model properties
     * @var array
     */
    protected array $properties = array(
        'id' => array(
            'description' => 'Question ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'question' => array(
            'description' => 'Security Question',
            'column' => 'question',
            'type' => 'varchar'
        ),
        'answer' => array(
            'description' => 'Security Answer',
            'column' => 'answer',
            'type' => 'varchar'
        ),
        'userId' => array(
            'description' => 'User ID',
            'column' => 'user_id',
            'type' => 'varchar'
        ),
        'created' => array(
            'description' => 'Date Created',
            'column' => 'created',
            'type' => 'datetime'
        ),
        'updated' => array(
            'description' => 'Date Updated',
            'column' => 'updated',
            'type' => 'datetime'
        )
    );

    /**
     * Hash the answer
     *
     * @param string $value Answer to question
     *
     * @return string Hashed answer
     */
    public function preAnswer(string $value): string
    {
        if (password_needs_rehash($value, PASSWORD_DEFAULT) === true) {
            $value = password_hash($value, PASSWORD_DEFAULT);
        }
        return $value;
    }

    /**
     * Verify the answer to the question
     *
     * @param string $value Answer input from user
     *
     * @return boolean Match/no match on answer
     */
    public function verifyAnswer(string $value): bool
    {
        if ($this->id === null) {
            return false;
        }
        return password_verify($value, $this->answer);
    }
}
