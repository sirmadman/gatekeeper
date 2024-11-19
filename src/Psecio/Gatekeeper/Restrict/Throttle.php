<?php

namespace Psecio\Gatekeeper\Restrict;

use Psecio\Gatekeeper\Gatekeeper;
use Psecio\Gatekeeper\ThrottleModel;
use Psecio\Gatekeeper\Restriction;

class Throttle extends Restriction
{
    public ThrottleModel $model;

    /**
    * Execute the evaluation for the restriction
    *
    * @return boolean Success/fail of evaluation
    */
    public function evaluate(): bool
    {
        $config = $this->getConfig();
        $throttle = Gatekeeper::getUserThrottle($config['userId']);
        $throttle->updateAttempts();
        $this->model = $throttle;

        // See if they're blocked
        if ($throttle->status === ThrottleModel::STATUS_BLOCKED) {
            $result = $throttle->checkTimeout();
            if ($result === false) {
                return false;
            }
        } else {
            $result = $throttle->checkAttempts();
            if ($result === false) {
                return false;
            }
        }
        return true;
    }
}
