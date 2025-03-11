<?php

namespace Ffhs\Approvals\Traits;

use Closure;
use Ffhs\Approvals\Approval\ApprovalBy;
use Ffhs\Approvals\Infolists\Actions\ApprovalByResetAction;

trait HasResetApprovalAction
{
    private Closure|bool $needResetApprovalBeforeChange = false;
    private Closure|null $modifyResetApprovalActionUsing = null;

    public function needResetApprovalBeforeChange(Closure|bool $needResetApprovalBeforeChange = true): static
    {
        $this->needResetApprovalBeforeChange = $needResetApprovalBeforeChange;

        return $this;
    }

    public function modifyResetApprovalActionUsing(Closure|null $modifyResetApprovalActionUsing): static
    {
        $this->modifyResetApprovalActionUsing = $modifyResetApprovalActionUsing;

        return $this;
    }

    public function getResetApprovalAction(ApprovalBy $approvalBy): ApprovalByResetAction
    {
        $action = ApprovalByResetAction::make($approvalBy->getName() . '-reset_approval')
            ->notificationOnResetApproval(fn($lastStatus) => $this->sendNotificationOnResetApproval($lastStatus))
            ->disabled($this->isApprovalActionsDisabled())
            ->approvalKey($this->getApprovalKey())
            ->approvalBy($approvalBy)
            ->size($this->getSize())
            ->visible(fn() => $this->isNeedResetApprovalBeforeChange());

        return $this->modifyResetApprovalAction($action, $approvalBy);
    }

    public function isNeedResetApprovalBeforeChange(): bool
    {
        return $this->evaluate($this->needResetApprovalBeforeChange);
    }

    public function modifyResetApprovalAction(
        ApprovalByResetAction $action,
        ApprovalBy $approvalBy
    ): ApprovalByResetAction {
        if (is_null($this->modifyResetApprovalActionUsing)) {
            return $action;
        }

        return $this->evaluate(
            $this->modifyResetApprovalActionUsing,
            [
                'action' => $action,
                'approvalBy' => $approvalBy,
            ],
            [
                ApprovalByResetAction::class => $action,
                ApprovalBy::class => $approvalBy,
            ]
        );
    }
}
