<?php

namespace Ffhs\Approvals\Filament\Actions;

use App\Models\User;
use Ffhs\Approvals\Concerns\HandlesApprovals;
use Ffhs\Approvals\Contracts\ApprovableByComponent;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Models\Approval;
use Ffhs\Approvals\Traits\Filament\HasApprovalNotification;
use Ffhs\Approvals\Traits\Filament\HasRecordUsing;
use Ffhs\Approvals\Traits\Filament\HasResetApprovalAction;
use Filament\Actions\Action;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class ApprovalSingleStateAction extends Action implements ApprovableByComponent
{
    use HandlesApprovals;
    use HasResetApprovalAction;
    use HasApprovalNotification;
    use HasRecordUsing;


    protected HasApprovalStatuses $actionCase;
    protected null|HasApprovalStatuses $approvalStatus = null;


    public function changeApproval(): void
    {
        if ($this->isActionActive()) {
            $oldState = $this->getApprovalStatus()->value;
            $this->getActionBoundApproval()?->delete();
            $this->sendNotificationOnRemoveApproval($oldState);
            $this->getRecord()?->refresh();

            return;
        }

        $isChanged = !is_null($this->getApprovalStatus());
        $oldState = $this->getApprovalStatus();
        $state = $this->getActionCaste();

        if ($isChanged) {
            $this->getActionBoundApproval()?->delete();
        }
        $stateString = $state?->value;

        Approval::create([
            'approver_id' => Auth::id(),
            'approver_type' => User::class,
            /** @phpstan-ignore-next-line */
            'approvable_id' => $this->approvable()->id,
            'approvable_type' => $this->approvable()::class,
            'status' => $stateString,
            'key' => $this->getApprovalKey(),
            'approval_by' => $this->getApprovalBy()->getName(),
        ]);

        if ($isChanged) {
            if (is_null($oldState) || is_null($state)) {
                throw new RuntimeException('Unexpectet error happend');
            }

            $this->sendNotificationOnChangeApproval($state, $oldState);
        } else {
            $this->sendNotificationOnSetApproval($stateString);
        }

        $this->getRecord()?->refresh();
    }

    public function isActionActive(): bool
    {
        return $this->getApprovalStatus() === $this->getActionCaste();
    }

    public function getApprovalStatus(): HasApprovalStatuses|null
    {
        return $this->getActionBoundApproval()?->status;
    }

    public function getActionBoundApproval(): ?Approval
    {
        return $this->getBoundApprovals()->first();
//            ->firstWhere(function (Approval $approval) {
//                return $approval->approver_id == Auth::id() &&
//                    $approval->approver_type == User::class;
//            }); //ToDo Later for at least
    }


    public function getActionCaste(): null|HasApprovalStatuses
    {
        return $this->approvalStatus;
    }

    public function getRecord(bool $withDefault = true): ?Model
    {
        return $this->getRecordFromUsing();
    }

    public function actionStatus(HasApprovalStatuses $approvalStatus): static
    {
        $this->approvalStatus = $approvalStatus;

        return $this;
    }


    public function isDisabled(): bool
    {
        if (($this->evaluate($this->isDisabled) || $this->isHidden()) || !$this->canApprove()) {
            return true;
        }

        if (!$this->isNeedResetApprovalBeforeChange()) {
            return false;
        }

        return !is_null($this->getApprovalStatus());
    }

    public function isHidden(): bool
    {
        if (parent::isHidden()) {
            return true;
        }

        if (!$this->isNeedResetApprovalBeforeChange() || is_null($this->getApprovalStatus())) {
            return false;
        }

        return $this->getApprovalStatus() !== $this->getActionCaste();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->name($this->name);
        $this->action($this->changeApproval(...));
        $this->iconPosition(IconPosition::After);
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'actionCase' => [$this->getActionCaste()],
            'actionCaseLabel' => [$this->getActionCaste()?->getCaseLabel($this->getActionCaste())],

            'state', 'status' => [$this->getApprovalStatus()],
            'approvalStatusLabel' => [$this->getApprovalStatus()?->getCaseLabel($this->getApprovalStatus())],
            'approvalFlow' => [$this->getApprovalFlow()],
            'approvalBy' => [$this->getApprovalBy()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }


}
