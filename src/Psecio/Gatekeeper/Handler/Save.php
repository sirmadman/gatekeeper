<?php

namespace Psecio\Gatekeeper\Handler;

use Psecio\Gatekeeper\Handler;

class Save extends Handler
{
    /**
    * Execute the save handling
    *
    * @return boolean Success/fail result of save
    */
    public function execute(): bool
    {
        $args = $this->getArguments();
        return $this - getDb()->save($args[0]);
    }
}
