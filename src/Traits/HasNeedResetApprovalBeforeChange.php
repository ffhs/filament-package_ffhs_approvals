<?php

namespace Ffhs\Approvals\Traits;

use Closure;

trait HasNeedResetApprovalBeforeChange
{
    private Closure|bool $needResetApprovalBeforeChange = false;

    public function needResetApprovalBeforeChange(Closure|bool $needResetApprovalBeforeChange = true): static{
        $this->needResetApprovalBeforeChange = $needResetApprovalBeforeChange;
        return $this;
    }

    public function isNeedResetApprovalBeforeChange(): bool
    {
        return $this->evaluate($this->needResetApprovalBeforeChange);
    }
}
