<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;

trait CanCasesDisable
{
    protected ?Closure $caseDisabledUsing = null;

    /** @var array<string, bool|Closure> */
    protected array|Closure $casesDisabled = [];

    public function isCaseDisabled(HasApprovalStatuses $actionCase): bool
    {
        if (is_null($this->caseDisabledUsing)) {
            return $this->isCaseDisabledDefault($actionCase);
        }

        return $this->evaluate($this->caseDisabledUsing, ['actionCase' => $actionCase]) ?? false;
    }

    public function isCaseDisabledDefault(HasApprovalStatuses $actionCase): bool
    {
        $caseDisabledList = $this->evaluate($this->casesDisabled)[$actionCase->value] ?? false;
        return $this->evaluate($caseDisabledList, ['actionCase' => $actionCase]);
    }

    public function caseDisabledUsing(Closure $caseDisabledUsing): static
    {
        $this->caseDisabledUsing = $caseDisabledUsing;
        return $this;
    }

    /**
     * @param array<string, bool|Closure> $caseDisabled
     * @return $this
     */
    public function casesDisabled(array|Closure $caseDisabled): static
    {
        $this->casesDisabled = $caseDisabled;
        return $this;
    }

    public function caseDisabled(
        HasApprovalStatuses $actionCase,
        bool|Closure $caseDisabled = true
    ): static {
        if ($this->caseDisabledUsing) {
            $this->caseDisabledUsing = null;
        }

        $this->casesDisabled[$actionCase->value] = $caseDisabled;

        return $this;
    }

}
