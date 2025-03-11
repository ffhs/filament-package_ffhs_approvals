<?php

namespace Ffhs\Approvals\Traits;

use BackedEnum;
use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;

trait HasHiddenCases
{
    private array|Closure $caseHidden = [];

    public function isCaseHidden(string|HasApprovalStatuses $status): bool
    {
        if ($status instanceof HasApprovalStatuses) {
            /** @var BackedEnum $status */
            $status = $status->value;
        }

        $isDisabled = $this->evaluate($this->caseHidden)[$status] ?? false;

        return $this->evaluate($isDisabled, ['status' => $status]);
    }

    public function caseHidden(array|Closure $caseHidden): static
    {
        $this->caseHidden = $caseHidden;

        return $this;
    }
}
