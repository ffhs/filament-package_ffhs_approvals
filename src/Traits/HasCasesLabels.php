<?php

namespace Ffhs\Approvals\Traits;

use BackedEnum;
use Closure;

trait HasCasesLabels
{
    private array|Closure $casesLabels = [];

    public function getCaseLabel(BackedEnum|string $approvalCase): string
    {
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        return $this->evaluate($this->getCaseIcons()[$approvalCase] ?? $approvalCase) ?? '';
    }

    public function getCaseLabels(): array
    {
        if (!is_array($this->casesLabels)) {
            $this->casesLabels = $this->evaluate($this->casesLabels);
        }
        return $this->casesLabels;
    }

    public function casesLabels(array|Closure $caseIcons): static
    {
        $this->casesLabels = $caseIcons;
        return $this;
    }

    public function caseLabel(BackedEnum|string $approvalCase, string|Closure $caseLabel): static
    {
        if ($this->casesLabels instanceof Closure) {
            $this->casesLabels = [];
        }
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        $this->casesLabels[$approvalCase] = $caseLabel;

        return $this;
    }

}
