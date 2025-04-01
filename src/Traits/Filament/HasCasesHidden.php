<?php

namespace Ffhs\Approvals\Traits\Filament;

use BackedEnum;
use Closure;

trait HasCasesHidden
{
    private array|Closure $casesHidden = [];

    public function isCaseHidden(BackedEnum|string $approvalCase): bool
    {
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }

        return $this->evaluate($this->getCasesHidden()[$approvalCase] ?? false);
    }


    public function getCasesHidden(): array
    {
        return $this->evaluate($this->casesHidden);
    }

    public function casesHidden(array|Closure $caseHidden): static
    {
        $this->casesHidden = $caseHidden;
        return $this;
    }

    public function caseHidden(BackedEnum|string $approvalCase, bool|Closure $caseHidden = true): static
    {
        if ($this->casesHidden instanceof Closure) {
            $this->casesHidden = [];
        }
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        $this->casesHidden[$approvalCase] = $caseHidden;

        return $this;
    }
}
