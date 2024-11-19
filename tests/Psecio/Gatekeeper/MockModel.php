<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Model\Mysql;

class MockModel extends Mysql
{
    protected $tableName = 'test';
}