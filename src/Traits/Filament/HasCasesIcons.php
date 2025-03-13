<?php

namespace Ffhs\Approvals\Traits\Filament;

use BackedEnum;
use Closure;

trait HasCasesIcons
{
    private array|Closure $casesIcons = [];

    public function getCaseIcon(BackedEnum|string $approvalCase): string|null
    {
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        return $this->evaluate($this->getCaseIcons()[$approvalCase] ?? null);
    }

    public function getCaseIcons(): array
    {
        if (!is_array($this->casesIcons)) {
            $this->casesIcons = $this->evaluate($this->casesIcons);
        }
        return $this->casesIcons;
    }

    public function casesIcons(array|Closure $caseIcons): static
    {
        $this->casesIcons = $caseIcons;
        return $this;
    }

    public function caseIcon(BackedEnum|string $approvalCase, string|Closure $caseIcon): static
    {
        if ($this->casesIcons instanceof Closure) {
            $this->casesIcons = [];
        }
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        $this->casesIcons[$approvalCase] = $caseIcon;

        return $this;
    }

}
