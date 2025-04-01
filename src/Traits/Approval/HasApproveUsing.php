<?php

namespace Ffhs\Approvals\Traits\Approval;

use Closure;

trait HasApproveUsing
{
    protected ?Closure $canApproveUsing = null;

    public function canApproveUsing(Closure $canApproveUsing): static
    {
        $this->canApproveUsing = $canApproveUsing;

        return $this;
    }

    public function getCanApproveUsing(): ?Closure
    {
        return $this->canApproveUsing;
    }
}
