<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Filament\Support\Colors\Color;

trait HasSelectedCaseColors
{
    protected ?Closure $caseSelectedColorUsing = null;

    /** @var array<string, mixed|Closure> */
    protected array|Closure $casesSelectedColors = [];

    /** @var string|string[]|Color|Closure|null */
    protected string|array|Color|Closure|null $casesDefaultSelectedColor = null;


    public function caseSelectedColorUsing(?Closure $caseSelectedColorUsing): static
    {
        $this->caseSelectedColorUsing = $caseSelectedColorUsing;
        return $this;
    }

    /**
     * @param array<string, mixed|Closure> $casesSelectedColors
     * @return $this
     */
    public function casesSelectedColors(array|Closure $casesSelectedColors): static
    {
        $this->casesSelectedColors = $casesSelectedColors;
        return $this;
    }

    public function caseSelectedColor(HasApprovalStatuses $actionCase, mixed $caseColor): static
    {
        if (!is_null($this->caseSelectedColorUsing)) {
            $this->caseSelectedColorUsing = null;
        }

        $this->casesSelectedColors[$actionCase->value] = $caseColor;

        return $this;
    }

    /**
     * @param string|string[]|Color|Closure $defaultColor
     * @return $this
     */
    public function caseDefaultSelectedColor(string|array|Color|Closure $defaultColor): static
    {
        $this->casesDefaultSelectedColor = $defaultColor;
        return $this;
    }


    public function getCaseSelectedColor(HasApprovalStatuses $actionCase): mixed
    {
        if (is_null($this->caseSelectedColorUsing)) {
            return $this->getCaseSelectedColorDefault($actionCase);
        }

        return $this->evaluate($this->caseSelectedColorUsing, ['approvalCase' => $actionCase])
            ?? $this->getCasesDefaultSelectedColor();
    }

    public function getCaseSelectedColorDefault(HasApprovalStatuses $actionCase): mixed
    {
        $caseColor = $this->evaluate($this->casesSelectedColors)[$actionCase->value] ?? null;
        return $this->evaluate($caseColor, ['approvalCase' => $actionCase])
            ?? $this->getCasesDefaultSelectedColor();
    }

    public function getCasesDefaultSelectedColor(): mixed
    {
        return $this->evaluate($this->casesDefaultSelectedColor);
    }


}
