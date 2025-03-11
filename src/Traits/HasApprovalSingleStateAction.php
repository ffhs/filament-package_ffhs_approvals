<?php

namespace Ffhs\Approvals\Traits;

use BackedEnum;
use Closure;
use Ffhs\Approvals\Approval\ApprovalBy;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Infolists\Actions\ApprovalSingleStateAction;
use UnitEnum;

trait HasApprovalSingleStateAction
{
    private Closure|null $modifyApprovalActionUsing = null;

    public function modifyApprovalActionUsing(Closure|null $modifyApprovalActionUsing): static
    {
        $this->modifyApprovalActionUsing = $modifyApprovalActionUsing;

        return $this;
    }

    public function getApprovalSingleStateAction(
        ApprovalBy $approvalBy,
        UnitEnum|HasApprovalStatuses $status
    ): ApprovalSingleStateAction {
        /** @var BackedEnum $status */
        $labelMap = $this->getApprovalActionsLabel();
        $label = $labelMap[$status->value] ?? $status->value;
        $toolTips = $this->getApprovalActionToolTips();


        $action = ApprovalSingleStateAction::make($approvalBy->getName() . '-' . $status->value)
            ->needResetApprovalBeforeChange($this->isNeedResetApprovalBeforeChange())
            ->approvalFlow($this->getApprovalFlow())
            ->requiresConfirmation($this->isRequiresConfirmation())
            ->colorSelected($this->getApprovalActionsSelectColor())
            ->colorNotSelected($this->getApprovalActionsColor())
            ->approvalIcons($this->getApprovalActionsIcons())
            ->notificationOnResetApproval(fn($lastStatus) => $this->sendNotificationOnResetApproval($lastStatus))
            ->notificationOnSetApproval(fn($status) => $this->sendNotificationOnSetApproval($status))
            ->notificationOnChangeApproval(
                fn($lastStatus, $status) => $this->sendNotificationOnChangeApproval($status, $lastStatus)
            )
            ->disabled(function () use ($status) {
                return $this->isApprovalActionsDisabled() || $this->isCaseDisabled($status->value);
            })
            ->approvalKey($this->getApprovalKey())
            ->tooltip($toolTips[$status->value] ?? null)
            ->label($label)
            ->size($this->getSize())
            ->approvalBy($approvalBy)
            ->actionStatus($status)
            ->hidden(function () use ($status) {
                return $this->isCaseHidden($status->value);
            });


        return $this->modifyApprovalSingleStateAction($action, $approvalBy, $status);
    }

    public function modifyApprovalSingleStateAction(
        ApprovalSingleStateAction $action,
        ApprovalBy $approvalBy,
        UnitEnum|HasApprovalStatuses $status
    ): ApprovalSingleStateAction {
        if (is_null($this->modifyApprovalActionUsing)) {
            return $action;
        }

        return $this->evaluate(
            $this->modifyApprovalActionUsing,
            [
                'action' => $action,
                'approvalBy' => $approvalBy,
                'status' => $status,
            ],
            [
                ApprovalSingleStateAction::class => $action,
                ApprovalBy::class => $approvalBy,
                HasApprovalStatuses::class => $status,
            ]
        );
    }
}
