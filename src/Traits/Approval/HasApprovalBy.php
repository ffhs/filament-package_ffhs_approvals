<?php

namespace Ffhs\Approvals\Traits\Approval;

use Closure;
use Ffhs\Approvals\Contracts\ApprovalBy;

trait HasApprovalBy
{
    /** @var ApprovalBy[]|Closure */
    protected array|Closure $approvalBy = [];

    /**
     * @return ApprovalBy[]
     */
    public function getApprovalBys(): array
    {
        return $this->evaluate($this->approvalBy);
    }

    /**
     * @param ApprovalBy[]|Closure $approvalBy
     * @return $this
     */
    public function approvalBy(array|Closure $approvalBy): static
    {
        $this->approvalBy = $approvalBy;

        return $this;
    }
}
