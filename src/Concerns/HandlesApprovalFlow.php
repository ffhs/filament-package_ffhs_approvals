<?php

namespace Ffhs\Approvals\Concerns;

use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Enums\ApprovalState;
use Illuminate\Database\Eloquent\Model;

trait HandlesApprovalFlow
{
    public function approved(Model|Approvable $approvable, string $key): ApprovalState
    {
        if ($this->isDisabled()) {
            return ApprovalState::APPROVED;
        }

        $isPending = false;
        $isOpen = false;

        foreach ($this->getApprovalBys() as $approvalBy) {
            $approved = $approvalBy->approved($approvable, $key);

            if ($approved == ApprovalState::PENDING) {
                $isPending = true;
            } elseif ($approved == ApprovalState::OPEN) {
                $isOpen = true;
            } elseif ($approved == ApprovalState::DENIED) {
                return ApprovalState::DENIED;
            }
        }

        if ($isPending) {
            return ApprovalState::PENDING;
        } elseif ($isOpen) {
            return ApprovalState::OPEN;
        }

        return ApprovalState::APPROVED;
    }
}
