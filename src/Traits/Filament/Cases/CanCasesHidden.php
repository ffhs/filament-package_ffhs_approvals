<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;

trait CanCasesHidden
{
    protected ?Closure $caseHiddenUsing = null;

    /** @var array<string, bool|Closure> */
    protected array|Closure $casesHidden = [];

    public function isCaseHidden(HasApprovalStatuses $actionCase): bool
    {
        if (is_null($this->caseHiddenUsing)) {
            return $this->isCaseHiddenDefault($actionCase);
        }


        return $this->evaluate($this->caseHiddenUsing, ['approvalCase' => $actionCase]) ?? false;
    }

    public function isCaseHiddenDefault(HasApprovalStatuses $actionCase): bool
    {
        $caseHiddenList = $this->evaluate($this->casesHidden)[$actionCase->value] ?? false;
        return $this->evaluate($caseHiddenList, ['approvalCase' => $actionCase]);
    }

    public function caseHiddenUsing(?Closure $caseHiddenUsing): static
    {
        $this->caseHiddenUsing = $caseHiddenUsing;
        return $this;
    }

    /**
     * @param array<string, bool|Closure>|Closure $caseHidden
     * @return $this
     */
    public function casesHidden(array|Closure $caseHidden): static
    {
        $this->casesHidden = $caseHidden;
        return $this;
    }

    public function caseHidden(HasApprovalStatuses $actionCase, bool|Closure $caseHidden = true): static
    {
        if (is_null($this->caseHiddenUsing)) {
            $this->caseHiddenUsing = null;
        }

        $this->casesHidden[$actionCase->value] = $caseHidden;

        return $this;
    }
}
