<?php

namespace Ffhs\Approvals\Traits;

use BackedEnum;
use Closure;
use Ffhs\Approvals\Contracts\ApprovalFlow;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;

trait HasCasesColors
{
    use HasApprovalStatusColor;

    private array|Closure $casesSelectedColors = [];
    private mixed $casesDefaultSelectedColor = null;
    private array|Closure $casesColors = [];
    private mixed $casesDefaultColor = 'gray';

    public function casesSelectedColors(array|Closure $caseIcons): static
    {
        $this->casesSelectedColors = $caseIcons;
        return $this;
    }

    public function caseSelectedColor(BackedEnum|string $approvalCase, mixed $caseColor): static
    {
        if ($this->casesSelectedColors instanceof Closure) {
            $this->casesSelectedColors = [];
        }
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        $this->casesSelectedColors[$approvalCase] = $caseColor;

        return $this;
    }

    public function caseDefaultSelectedColor(mixed $defaultColor): static
    {
        $this->casesDefaultSelectedColor = $defaultColor;
        return $this;
    }

    public function casesColors(array|Closure $caseIcons): static
    {
        $this->casesColors = $caseIcons;
        return $this;
    }

    public function caseColor(BackedEnum|string $approvalCase, mixed $caseColor): static
    {
        if ($this->casesColors instanceof Closure) {
            $this->casesColors = [];
        }
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        $this->casesColors[$approvalCase] = $caseColor;

        return $this;
    }

    public function caseDefaultColor(mixed $defaultColor): static
    {
        $this->casesDefaultColor = $defaultColor;
        return $this;
    }

    public function getFinalCaseColor(
        BackedEnum|string $approvalCase,
        BackedEnum|null|HasApprovalStatuses $currentApproval,
        ApprovalFlow $flow
    ): mixed {
        // Wenn der Status nicht gewählt ist
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }

        if ($approvalCase != $currentApproval?->value) {
            return $this->getCaseColor($approvalCase);
        }

        $color = $this->getCaseSelectedColor($currentApproval);

        if (!is_null($color)) return $color;

        return $this->getApprovalStatusColor($approvalCase, $flow);
    }

    public function getCaseColor(BackedEnum|string $approvalCase): mixed
    {
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        return $this->evaluate($this->getCaseColors()[$approvalCase] ?? null) ?? $this->getCasesDefaultColor();
    }

    public function getCaseColors(): array
    {
        if (!is_array($this->casesColors)) {
            $this->casesColors = $this->evaluate($this->casesColors);
        }
        return $this->casesColors;
    }

    public function getCasesDefaultColor(): mixed
    {
        return $this->evaluate($this->casesDefaultColor);
    }

    public function getCaseSelectedColor(BackedEnum|string $approvalCase): mixed
    {
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }
        return $this->evaluate(
            $this->getCaseSelectedColors()[$approvalCase] ?? null
        ) ?? $this->getCasesDefaultSelectedColor();
    }

    public function getCaseSelectedColors(): array
    {
        if (!is_array($this->casesSelectedColors)) {
            $this->casesSelectedColors = $this->evaluate($this->casesSelectedColors);
        }
        return $this->casesSelectedColors;
    }

    public function getCasesDefaultSelectedColor(): mixed
    {
        return $this->evaluate($this->casesDefaultSelectedColor);
    }

}
