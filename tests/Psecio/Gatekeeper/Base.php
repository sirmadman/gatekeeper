<?php

namespace Psecio\Gatekeeper;

include_once __DIR__ . '/MockPdo.php';
include_once __DIR__ . '/MockModel.php';

use PHPUnit\Framework\TestCase;
use Psecio\Gatekeeper\DataSource\Stub;

class Base extends TestCase
{
    public function buildMock(mixed $return, string $type = 'find'): object
    {
        $ds = $this->getMockBuilder(Stub::class)
            ->setConstructorArgs(array(array()))
            ->getMock();
        $ds->method($type)
            ->willReturn($return);

        return $ds;
    }
}
