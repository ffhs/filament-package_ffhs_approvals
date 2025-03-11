<?php

namespace Ffhs\Approvals\Traits;

use Closure;
use Enum;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;

trait HasDisableCase
{
    private array|Closure $caseDisabled = [];

    public function isCaseDisabled(string|HasApprovalStatuses $status): bool
    {
        if ($status instanceof HasApprovalStatuses) {
            /** @var Enum $status */
            $status = $status->value;
        }

        $isDisabled = $this->evaluate($this->caseDisabled)[$status] ?? false;

        return $this->evaluate($isDisabled, ['status' => $status]);
    }

    public function caseDisabled(array|Closure $caseDisabled): static
    {
        $this->caseDisabled = $caseDisabled;

        return $this;
    }
}
