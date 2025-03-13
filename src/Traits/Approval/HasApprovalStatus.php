<?php

namespace Ffhs\Approvals\Traits\Approval;

use BackedEnum;
use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;

trait HasApprovalStatus
{
    protected array|Closure $approvalStatus;

    public function approvalStatus(array|Closure $approvalStatus): static
    {
        $this->approvalStatus = $approvalStatus;

        return $this;
    }

    public function getStatusEnumClass(): ?string
    {
        if (empty($this->getApprovalStatus())) {
            return null;
        }

        return $this->getApprovalStatus()[0]::class;
    }


    /**
     * @return array<BackedEnum|HasApprovalStatuses>
     */
    public function getApprovalStatus(): array
    {
        return $this->evaluate($this->approvalStatus);
    }

}
