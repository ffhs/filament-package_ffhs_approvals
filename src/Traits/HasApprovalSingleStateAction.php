<?php

namespace Ffhs\Approvals\Traits;

use BackedEnum;
use Closure;
use Ffhs\Approvals\Approval\ApprovalBy;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Infolists\Actions\ApprovalSingleStateAction;
use Filament\Actions\Concerns\CanBeDisabled;
use Filament\Actions\Concerns\HasSize;
use UnitEnum;

trait HasApprovalSingleStateAction
{
    use HasCasesToolTips;
    use HasCasesDisable;
    use HasCasesHidden;
    use HasCasesIcons;
    use HasSize;
    use HasCasesLabels;
    use CanBeDisabled;

    private Closure|null $modifyApprovalActionUsing = null;

    public function modifyApprovalActionUsing(Closure|null $modifyApprovalActionUsing): static
    {
        $this->modifyApprovalActionUsing = $modifyApprovalActionUsing;

        return $this;
    }

    public function getApprovalSingleStateAction(
        ApprovalBy $approvalBy,
        UnitEnum|HasApprovalStatuses $approvalCase
    ): ApprovalSingleStateAction {
        /** @var BackedEnum $approvalCase */

        $action = ApprovalSingleStateAction::make($approvalBy->getName() . '-' . $approvalCase->value)
            ->needResetApprovalBeforeChange($this->isNeedResetApprovalBeforeChange())
            ->approvalFlow($this->getApprovalFlow())
            ->requiresConfirmation($this->isRequiresConfirmation())
            ->colorSelected($this->getApprovalActionsSelectColor())
            ->colorNotSelected($this->getApprovalActionsColor())
            ->icon($this->getCaseIcon($approvalCase))
            ->notificationOnResetApproval(fn($lastStatus) => $this->sendNotificationOnResetApproval($lastStatus))
            ->notificationOnSetApproval(fn($status) => $this->sendNotificationOnSetApproval($status))
            ->notificationOnChangeApproval(
                fn($lastStatus, $status) => $this->sendNotificationOnChangeApproval($status, $lastStatus)
            )
            ->disabled(function () use ($approvalCase) {
                return $this->isDisabled() || $this->isCaseDisabled($approvalCase->value);
            })
            ->approvalKey($this->getApprovalKey())
            ->tooltip($this->getCaseTooltip($approvalCase))
            ->label($this->getCaseLabel($approvalCase))
            ->size($this->getSize())
            ->approvalBy($approvalBy)
            ->actionStatus($approvalCase)
            ->hidden(function () use ($approvalCase) {
                return $this->isCaseHidden($approvalCase->value);
            });


        return $this->modifyApprovalSingleStateAction($action, $approvalBy, $approvalCase);
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
