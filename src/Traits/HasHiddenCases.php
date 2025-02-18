<?php


namespace Ffhs\Approvals\Traits;


use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;

trait HasHiddenCases
{

    private array|Closure $caseHidden = [];

    public function isCaseHidden(string|HasApprovalStatuses $status): bool
    {
        if ($status instanceof HasApprovalStatuses) {
            $status = $status->value;
        }
        $isDisabled = $this->evaluate($this->caseDisabled)[$status] ?? false;
        return $this->evaluate($isDisabled);
    }

    public function caseHidden(array|Closure $caseDisabled): static
    {
        $this->caseDisabled = $caseDisabled;
        return $this;
    }


}
