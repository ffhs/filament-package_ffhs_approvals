<?php

namespace Ffhs\Approvals\Traits\Approval;

use Closure;
use Ffhs\Approvals\Contracts\ApprovalBy;

trait HasApprovalBy
{

    protected array|Closure $approvalBy = [];

    /**
     * @return array<ApprovalBy>
     */
    public function getApprovalBys(): array
    {
        return $this->evaluate($this->approvalBy);
    }

    public function approvalBy(array|Closure $approvalBy): static
    {
        $this->approvalBy = $approvalBy;

        return $this;
    }
}
