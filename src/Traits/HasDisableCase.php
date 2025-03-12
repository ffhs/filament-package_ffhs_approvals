<?php

namespace Ffhs\Approvals\Traits;

use BackedEnum;
use Closure;

trait HasDisableCase
{
    private array|Closure $casesDisabled = [];

    public function isCaseDisabled(string|BackedEnum $approvalCase): bool
    {
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        return $this->evaluate($this->getCasesDisabled()[$approvalCase] ?? false);
    }

    public function getCasesDisabled(): array
    {
        return $this->evaluate($this->casesDisabled);
    }

    public function casesDisabled(array|Closure $casesDisabled): static
    {
        $this->casesDisabled = $casesDisabled;

        return $this;
    }

    public function caseDisabled(BackedEnum|string $approvalCase, bool|Closure $caseDisabled = true): static
    {
        if ($this->caseDisabled instanceof Closure) {
            $this->caseDisabled = [];
        }
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        $this->caseDisabled[$approvalCase] = $caseDisabled;

        return $this;
    }
}
