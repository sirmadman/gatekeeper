<?php

namespace Psecio\Gatekeeper\Resolve;

use Psecio\Gatekeeper\UserModel;
use Psecio\Gatekeeper\UserPermissionCollection;
use Psecio\Gatekeeper\Resolve;

class Permissions extends Resolve
{
    /**
    * Resolve the user's immediate permissions (directly on the user
    *   and on the groups the user belongs to)
    *
    * @param \Psecio\Gatekeeper\UserModel $user UserModel instance
    *
    * @return \Psecio\Gatekeeper\UserPermissionCollection instance
    */
    public function resolve(UserModel $user): UserPermissionCollection
    {
        // Start with the user's direct permissions
        $permissions = $user->permissions;

        // Now find the ones in the user's groups too
        foreach ($user->groups as $group) {
            foreach ($group->permissions as $permission) {
                $permissions->add($permission);
            }
        }

        return $permissions;
    }
}
