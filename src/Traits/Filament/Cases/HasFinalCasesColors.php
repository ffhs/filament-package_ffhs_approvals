<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Traits\Filament\HasApprovalStatusColor;

trait HasFinalCasesColors
{
    use HasApprovalStatusColor;
    use HasSelectedCaseColors;
    use HasCasesColors;

    public function getFinalCaseColor(
        HasApprovalStatuses $actionCase,
        null|HasApprovalStatuses $state
    ): mixed {
        $color = $actionCase === $state
            ? $this->getCaseSelectedColor($actionCase)
            : $this->getCaseColor($actionCase);

        return $color ?? $this->getApprovalStatusColor($actionCase);
    }

}
