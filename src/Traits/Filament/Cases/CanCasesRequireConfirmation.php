<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;

trait CanCasesRequireConfirmation
{
    protected ?Closure $caseRequiresConfirmationUsing = null;

    /** @var array<string, bool|Closure> */
    protected array|Closure $casesRequiresConfirmation = [];

    public function isCaseRequiresConfirmation(HasApprovalStatuses $actionCase): bool
    {
        if (is_null($this->caseRequiresConfirmationUsing)) {
            return $this->isCaseRequiresConfirmationDefault($actionCase->value);
        }

        return $this->evaluate($this->caseRequiresConfirmationUsing, ['approvalCase' => $actionCase]) ?? false;
    }

    public function isCaseRequiresConfirmationDefault(string $actionCase): bool
    {
        $caseRequiresConfirmation = $this->evaluate($this->casesRequiresConfirmation)[$actionCase] ?? false;
        return $this->evaluate($caseRequiresConfirmation, ['approvalCase' => $actionCase]);
    }

    public function caseRequiresConfirmationUsing(?Closure $caseRequiresConfirmationUsing): static
    {
        $this->caseRequiresConfirmationUsing = $caseRequiresConfirmationUsing;
        return $this;
    }

    /**
     * @param array<string, bool|Closure> $casesRequiresConfirmation
     * @return $this
     */
    public function casesRequiresConfirmation(array|Closure $casesRequiresConfirmation): static
    {
        $this->casesRequiresConfirmation = $casesRequiresConfirmation;
        return $this;
    }

    public function caseRequiresConfirmation(
        HasApprovalStatuses $actionCase,
        bool|Closure $caseRequiresConfirmation = true
    ): static {
        if (!is_null($this->caseRequiresConfirmationUsing)) {
            $this->caseRequiresConfirmationUsing = null;
        }

        $this->casesRequiresConfirmation[$actionCase->value] = $caseRequiresConfirmation;

        return $this;
    }
}
