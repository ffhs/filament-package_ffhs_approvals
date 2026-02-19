<?php

namespace Ffhs\Approvals\Traits\Approval;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;

trait HasApprovalStatus
{
    /** @var HasApprovalStatuses[]|Closure|class-string<HasApprovalStatuses> */
    protected array|Closure|string $approvalStatus;

    /**
     * @param HasApprovalStatuses[]|Closure|class-string<HasApprovalStatuses> $approvalStatus
     * @return $this
     */
    public function approvalStatus(array|Closure|string $approvalStatus): static
    {
        $this->approvalStatus = $approvalStatus;

        return $this;
    }

    /**
     * @return null|class-string<HasApprovalStatuses>
     */
    public function getStatusEnumClass(): ?string
    {
        $approvalStatus = $this->evaluate($this->approvalStatus);
        if (empty($approvalStatus)) {
            return null;
        }

        if (is_string($approvalStatus)) {
            return $approvalStatus;
        }

        return $approvalStatus[0]::class;
    }


    /**
     * @return HasApprovalStatuses[]
     */
    public function getApprovalStatus(): array
    {
        $approvalStatus = $this->evaluate($this->approvalStatus);

        /**@phpstan-ignore-next-line */
        return is_string($approvalStatus)
            ? $approvalStatus::cases
            : $approvalStatus;
    }

}
