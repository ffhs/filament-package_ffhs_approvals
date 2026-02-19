<?php

namespace Ffhs\Approvals\Approval;

use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Contracts\ApprovalFlow;
use Ffhs\Approvals\Enums\ApprovalState;
use Ffhs\Approvals\Traits\Approval\CanBeDisabled;
use Ffhs\Approvals\Traits\Approval\HasApprovalBy;
use Ffhs\Approvals\Traits\Approval\HasApprovalStatus;
use Ffhs\Approvals\Traits\Approval\HasCategory;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Database\Eloquent\Model;

class SimpleApprovalFlow implements ApprovalFlow
{
    use HasCategory;
    use HasApprovalBy;
    use HasApprovalStatus;
    use EvaluatesClosures;
    use CanBeDisabled;

    public static function make(): static
    {
        $approvalFlow = app(static::class);
        $approvalFlow->setUp();

        return $approvalFlow;
    }

    protected function setUp(): void
    {
    }


    public function approved(Model|Approvable $approvable, string $key): ApprovalState
    {
        if ($this->isDisabled()) {
            return ApprovalState::APPROVED;
        }

        $isPending = false;
        $isOpen = false;

        foreach ($this->getApprovalBys() as $approvalBy) {
            $approved = $approvalBy->approved($approvable, $key);

            if ($approved === ApprovalState::PENDING) {
                $isPending = true;
            } elseif ($approved === ApprovalState::OPEN) {
                $isOpen = true;
            } elseif ($approved === ApprovalState::DENIED) {
                return ApprovalState::DENIED;
            }
        }

        if ($isPending) {
            return ApprovalState::PENDING;
        }

        if ($isOpen) {
            return ApprovalState::OPEN;
        }

        return ApprovalState::APPROVED;
    }

}
