<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Base;

class DotenvLoadTest extends Base
{
    public function testLoadConfig()
    {
      $config = [];
      $result = Gatekeeper::loadConfig($config);
      $this->assertIsArray($result);
    }
}