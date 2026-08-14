<?php

namespace App\Events;

use App\Models\Permission;

class PermissionCreated
{
    public function __construct(
        public Permission $permission
    ) {}
}
