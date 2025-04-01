<?php

namespace Ffhs\Approvals\Traits\Approval;

use BackedEnum;
use Closure;

trait HasRoles
{
    protected string|Closure|BackedEnum|null $role = null;

    public function role(string|Closure|BackedEnum|null $role): static
    {
        $this->role = $role;

        return $this;
    }


    public function getRole(): ?string
    {
        return $this->role;
    }

}
