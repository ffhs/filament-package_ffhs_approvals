<?php

namespace Ffhs\Approvals\Traits\Filament;

use Closure;
use Ffhs\Approvals\Contracts\ApprovalBy;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Filament\Actions\ApprovalSingleStateAction;
use Ffhs\Approvals\Traits\Filament\Cases\CanCasesDisable;
use Ffhs\Approvals\Traits\Filament\Cases\CanCasesHidden;
use Ffhs\Approvals\Traits\Filament\Cases\CanCasesRequireConfirmation;
use Ffhs\Approvals\Traits\Filament\Cases\HasCasesApprovalNotifications;
use Ffhs\Approvals\Traits\Filament\Cases\HasCasesIcons;
use Ffhs\Approvals\Traits\Filament\Cases\HasCasesLabels;
use Ffhs\Approvals\Traits\Filament\Cases\HasCasesToolTips;
use Ffhs\Approvals\Traits\Filament\Cases\HasFinalCasesColors;
use Filament\Actions\Concerns\CanBeDisabled;
use Filament\Actions\Concerns\HasSize;
use UnitEnum;

trait HasApprovalSingleStateAction
{
    use CanBeDisabled;
    use CanCasesDisable;
    use CanCasesHidden;
    use CanCasesRequireConfirmation;
    use HasCasesIcons;
    use HasCasesToolTips;
    use HasCasesLabels;
    use HasFinalCasesColors;
    use HasCasesApprovalNotifications;

    use HasSize;

    private Closure|null $modifyApprovalActionUsing = null;

    public function modifyApprovalActionUsing(Closure|null $modifyApprovalActionUsing): static
    {
        $this->modifyApprovalActionUsing = $modifyApprovalActionUsing;

        return $this;
    }

    public function getApprovalSingleStateAction(
        ApprovalBy $approvalBy,
        HasApprovalStatuses $actionCase
    ): ApprovalSingleStateAction {
        $actionName = $approvalBy->getName() . '-' . $actionCase->value;
        $action = ApprovalSingleStateAction::make($actionName)
            ->approvalFlow($this->getApprovalFlow())
            ->approvalKey($this->getApprovalKey())
            ->recordUsing($this->getRecord())
            ->actionStatus($actionCase)
            ->approvalBy($approvalBy);

        //Static Changes
        $action = $action
            ->needResetApprovalBeforeChange($this->isNeedResetApprovalBeforeChange(...))
            ->size($this->getSize(...));

        //Cases Settings
        $action = $action
            ->requiresConfirmation($this->isCaseRequiresConfirmation($actionCase))
            ->disabled(function (ApprovalSingleStateAction $action) {
                /**@phpstan-ignore-next-line */
                return $this->isDisabled() || $action->evaluate($this->isCaseDisabled(...));
            })
            ->hidden(fn() => $this->isCaseHidden($actionCase))
            ->icon($this->getCaseIcon($actionCase))
            ->tooltip($this->getCaseTooltip($actionCase))
            ->label($this->getCaseLabel($actionCase));

        //Color
        $action = $action
            ->color($this->getFinalCaseColor(...));

        //Notifications
        $action = $action
            ->notificationOnSetApproval($this->getCaseNotificationOnSetApproval(...))
            ->notificationOnChangeApproval($this->getCaseNotificationOnChangeApproval(...))
            ->notificationOnRemoveApproval($this->getCaseNotificationOnRemoveApproval(...));

        return $this->modifyApprovalSingleStateAction($action, $approvalBy, $actionCase);
    }

    public function isDisabled(): bool
    {
        if ($this->evaluate($this->isDisabled)) {
            return true;
        }

        return $this->isHidden();
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
