<?php

namespace Ffhs\Approvals\Traits\Filament;

use BackedEnum;
use Closure;

trait HasCasesToolTips
{
    private array|Closure $casesToolTips = [];

    public function getCaseTooltip(string|BackedEnum $approvalCase): string|null
    {
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        return $this->evaluate($this->getCasesToolTips()[$approvalCase] ?? null);
    }

    public function getCasesToolTips(): array
    {
        if (!is_array($this->casesToolTips)) {
            $this->casesToolTips = $this->evaluate($this->casesToolTips);
        }
        return $this->casesToolTips;
    }

    public function caseTooltip(BackedEnum|string $approvalCase, string|Closure $caseToolTip): static
    {
        if ($this->casesToolTips instanceof Closure) {
            $this->casesToolTips = [];
        }
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        $this->casesToolTips[$approvalCase] = $caseToolTip;

        return $this;
    }


    public function casesToolTips(array|Closure $approvalActionToolTips): static
    {
        $this->casesToolTips = $approvalActionToolTips;
        return $this;
    }


}
