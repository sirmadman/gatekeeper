<?php

namespace Psecio\Gatekeeper;

use PHPUnit\Framework\TestCase;

class Base extends TestCase
{
    public function buildMock($return, $type = 'find')
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Stub')
            ->setConstructorArgs(array(array()))
            ->getMock();
        $ds->method($type)
            ->willReturn($return);

        return $ds;
    }
}