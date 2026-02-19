<?php

namespace Ffhs\Approvals\Traits\Filament;

use Closure;
use Ffhs\Approvals\Contracts\ApprovalBy;
use Ffhs\Approvals\Filament\Actions\ApprovalByResetAction;

trait HasResetApprovalAction
{
    use HasApprovalResetNotification;

    private Closure|bool $needResetApprovalBeforeChange = false;
    private Closure|null $modifyResetApprovalActionUsing = null;
    private Closure|bool $resetRequiresConfirmation = false;

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

    public function isResetRequiresConfirmation(): bool
    {
        return $this->evaluate($this->resetRequiresConfirmation) ?? false;
    }

    public function getResetApprovalAction(ApprovalBy $approvalBy): ApprovalByResetAction
    {
        $action = ApprovalByResetAction::make($approvalBy->getName() . '-reset_approval')
            ->requiresConfirmation($this->isResetRequiresConfirmation(...))
            ->notificationOnResetApproval($this->notificationOnResetApproval)
            ->visible($this->isNeedResetApprovalBeforeChange(...))
            ->disabled($this->isDisabled(...))
            ->recordUsing($this->getRecord(...))
            ->approvalKey($this->getApprovalKey())
            ->approvalBy($approvalBy)
            ->size($this->getSize());

        return $this->modifyResetApprovalAction($action, $approvalBy);
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

    public function isNeedResetApprovalBeforeChange(): bool
    {
        return $this->evaluate($this->needResetApprovalBeforeChange);
    }
}
