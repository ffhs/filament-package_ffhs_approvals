<?php

namespace Ffhs\Approvals\Traits\Filament;

use BackedEnum;
use Closure;
use Ffhs\Approvals\Contracts\ApprovalBy;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Filament\Actions\ApprovalSingleStateAction;
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
    use HasCasesColors;

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
        /** @var BackedEnum|HasApprovalStatuses $approvalCase */

        $action = ApprovalSingleStateAction::make($approvalBy->getName() . '-' . $approvalCase->value)
            ->needResetApprovalBeforeChange($this->isNeedResetApprovalBeforeChange())
            ->approvalFlow($this->getApprovalFlow())
            ->recordUsing(fn() => $this->getRecord())
            ->requiresConfirmation($this->isRequiresConfirmation())
            ->color(function (ApprovalSingleStateAction $action) use ($approvalCase) {
                return $this->getFinalCaseColor(
                    $approvalCase,
                    $action->getApprovalStatus(),
                    $this->getApprovalFlow()
                );
            })
            ->icon($this->getCaseIcon($approvalCase))
            ->notificationOnResetApproval(fn($lastStatus) => $this->sendNotificationOnResetApproval($lastStatus))
            ->notificationOnSetApproval(fn($status) => $this->sendNotificationOnSetApproval($status))
            ->notificationOnChangeApproval(
                fn($lastStatus, $status) => $this->sendNotificationOnChangeApproval($status, $lastStatus)
            )
            ->disabled(function () use ($approvalCase) {
                return $this->isDisabled() || $this->isCaseDisabled($approvalCase->value);
            })
            ->hidden(fn() => $this->isCaseHidden($approvalCase->value))
            ->tooltip($this->getCaseTooltip($approvalCase))
            ->label($this->getCaseLabel($approvalCase))
            ->approvalKey($this->getApprovalKey())
            ->actionStatus($approvalCase)
            ->approvalBy($approvalBy)
            ->size($this->getSize());


        return $this->modifyApprovalSingleStateAction($action, $approvalBy, $approvalCase);
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
