<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Filament\Support\Colors\Color;

trait HasCasesColors
{
    protected ?Closure $caseColorUsing = null;

    /** @var array<string, mixed|Closure> */
    protected array|Closure $casesColors = [];

    /** @var string|string[]|Color|Closure|null */
    protected string|array|Color|Closure|null $casesDefaultColor = 'gray';

    public function caseColorUsing(?Closure $caseColorUsing): static
    {
        $this->caseColorUsing = $caseColorUsing;
        return $this;
    }

    /**
     * @param array<string, mixed|Closure> $casesColors
     * @return $this
     */
    public function casesColors(array|Closure $casesColors): static
    {
        $this->casesColors = $casesColors;
        return $this;
    }

    public function caseColor(HasApprovalStatuses $actionCase, mixed $caseColor): static
    {
        if (!is_null($this->caseColorUsing)) {
            $this->caseColorUsing = null;
        }

        $this->casesColors[$actionCase->value] = $caseColor;

        return $this;
    }

    /**
     * @param string|string[]|Color|Closure $defaultColor
     * @return $this
     */
    public function caseDefaultColor(string|array|Color|Closure $defaultColor): static
    {
        $this->casesDefaultColor = $defaultColor;
        return $this;
    }

    public function getCaseColor(HasApprovalStatuses $actionCase): mixed
    {
        if (is_null($this->caseColorUsing)) {
            return $this->getCaseColorDefault($actionCase->value);
        }

        return $this->evaluate($this->caseColorUsing, ['approvalCase' => $actionCase])
            ?? $this->getCasesDefaultColor();
    }

    public function getCaseColorDefault(string $actionCase): mixed
    {
        $caseColor = $this->evaluate($this->casesColors)[$actionCase] ?? null;
        return $this->evaluate($caseColor, ['approvalCase' => $actionCase])
            ?? $this->getCasesDefaultColor();
    }

    public function getCasesDefaultColor(): mixed
    {
        return $this->evaluate($this->casesDefaultColor);
    }
}
