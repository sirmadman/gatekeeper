<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Base;

class DotenvLoadTest extends Base
{
    public function testLoadConfig()
    {
        $config = [
            'DB_USER' => 'username',
            'DB_PASS' => 'password',
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'Gatekeeper',
        ];
        $result = Gatekeeper::loadConfig($config);
        $this->assertIsArray($result);
    }

    public function testLoadConfigEnv()
    {
        $config = [];
        $result = Gatekeeper::loadConfig($config);
        $this->assertIsArray($result);
    }
}