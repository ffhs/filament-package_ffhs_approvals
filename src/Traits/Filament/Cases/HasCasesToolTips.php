<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use BackedEnum;
use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;

trait HasCasesToolTips
{
    protected ?Closure $caseTooltipUsing = null;

    /** @var array<string, string|null|Closure> */
    private array|Closure $casesToolTips = [];

    public function getCaseTooltip(string|BackedEnum $actionCase): string|null
    {
        if (!is_string($actionCase)) {
            $actionCase = $actionCase->value;
        }

        if (!is_null($this->caseTooltipUsing)) {
            return $this->getCaseTooltipDefault($actionCase);
        }

        return $this->evaluate($this->caseTooltipUsing, ['approvalCase' => $actionCase]);
    }

    public function getCaseTooltipDefault(string $actionCase): string|null
    {
        $caseTooltip = $this->evaluate($this->casesToolTips)[$actionCase] ?? null;
        return $this->evaluate($caseTooltip, ['approvalCase' => $actionCase]);
    }

    public function caseTooltipUsing(?Closure $caseTooltipUsing): static
    {
        $this->caseTooltipUsing = $caseTooltipUsing;
        return $this;
    }

    /**
     * @param array<string, string|null|Closure> $approvalActionToolTips
     * @return $this
     */
    public function casesToolTips(array|Closure $approvalActionToolTips): static
    {
        $this->casesToolTips = $approvalActionToolTips;
        return $this;
    }

    public function caseTooltip(HasApprovalStatuses $actionCase, string|null|Closure $caseToolTip): static
    {
        if (!is_null($this->caseTooltipUsing)) {
            $this->caseTooltipUsing = null;
        }

        $this->casesToolTips[$actionCase->value] = $caseToolTip;

        return $this;
    }

}
