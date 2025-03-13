<?php

namespace Ffhs\Approvals\Traits\Approval;

use Closure;

trait CanBeAny
{
    protected bool|Closure $any = false;


    public function any(bool $any = true): static
    {
        $this->any = $any;

        return $this;
    }

    public function isAny(): bool
    {
        return $this->any;
    }


}
