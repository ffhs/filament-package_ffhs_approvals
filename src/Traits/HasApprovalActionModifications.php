<?php

namespace Ffhs\Approvals\Traits;

use Closure;

trait HasApprovalActionModifications
{
    private array|Closure $approvalActionsGroupLabel = [];
    private ?array $cachedApprovalActionsGroupLabel = null;


    public function approvalActionsGroupLabel(array|Closure $approvalActionsGroupLabel): static
    {
        $this->approvalActionsGroupLabel = $approvalActionsGroupLabel;
        $this->cachedApprovalActionsGroupLabel = null;

        return $this;
    }

    public function getApprovalActionsGroupLabel(): array
    {
        if (!is_null($this->cachedApprovalActionsGroupLabel)) {
            return $this->cachedApprovalActionsGroupLabel;
        }

        $this->cachedApprovalActionsGroupLabel = $this->evaluate($this->approvalActionsGroupLabel);

        return $this->cachedApprovalActionsGroupLabel;
    }


}
