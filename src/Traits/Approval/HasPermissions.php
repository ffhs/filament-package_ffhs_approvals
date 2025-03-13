<?php

namespace Ffhs\Approvals\Traits\Approval;

use BackedEnum;
use Closure;

trait HasPermissions
{
    protected string|Closure|BackedEnum|null $permission = null;

    public function permission(string|Closure|BackedEnum|null $permission): static
    {
        $this->permission = $permission;

        return $this;
    }

    public function getPermission(): ?string
    {
        $permission = $this->evaluate($this->permission);
        if ($permission instanceof BackedEnum) {
            $permission = $permission->value;
        }

        return $permission;
    }


}
