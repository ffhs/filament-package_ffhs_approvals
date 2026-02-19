<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;


trait HasCasesLabels
{
    protected ?Closure $caseLabelUsing = null;

    /** @var array<string, string|Closure> */
    private array|Closure $casesLabels = [];

    public function getCaseLabel(HasApprovalStatuses $actionCase): string
    {
        if (is_null($this->caseLabelUsing)) {
            return $this->getCaseLabelDefault($actionCase);
        }

        return $this->evaluate($this->caseLabelUsing, ['approvalCase' => $actionCase]) ?? $actionCase->value;
    }

    public function getCaseLabelDefault(HasApprovalStatuses $actionCase): string
    {
        $caseLabel = $this->evaluate($this->casesLabels)[$actionCase->value] ?? $actionCase->value;
        return $this->evaluate($caseLabel, ['approvalCase' => $actionCase]);
    }

    public function caseLabelUsing(?Closure $caseLabelUsing): static
    {
        $this->caseLabelUsing = $caseLabelUsing;
        return $this;
    }

    /**
     * @param array<string, string|Closure> $casesLabels
     * @return $this
     */
    public function casesLabels(array|Closure $casesLabels): static
    {
        $this->casesLabels = $casesLabels;
        return $this;
    }

    public function caseLabel(HasApprovalStatuses $actionCase, string|Closure $caseLabel): static
    {
        if (!is_null($this->caseLabelUsing)) {
            $this->caseLabelUsing = null;
        }

        $this->casesLabels[$actionCase->value] = $caseLabel;

        return $this;
    }
}
