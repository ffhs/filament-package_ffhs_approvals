<?php

namespace Ffhs\Approvals\Filament\Actions;

use App\Models\User;
use BackedEnum;
use Ffhs\Approvals\Concerns\HandlesApprovals;
use Ffhs\Approvals\Contracts\ApprovableByComponent;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Models\Approval;
use Ffhs\Approvals\Traits\Filament\HasApprovalNotification;
use Ffhs\Approvals\Traits\Filament\HasRecordUsing;
use Ffhs\Approvals\Traits\Filament\HasResetApprovalAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ApprovalSingleStateAction extends Action implements ApprovableByComponent
{
    use HandlesApprovals;
    use HasResetApprovalAction;
    use HasApprovalNotification;
    use HasRecordUsing;

    protected ?HasApprovalStatuses $approvalStatus = null;


    public function changeApproval(): void
    {
        if ($this->isActionActive()) {
            $this->getActionBoundApproval()?->delete();

            Notification::make()
                ->title('remove approval') //ToDo Translate and add text state
                ->success()
                ->send();

            $this->getRecord()?->refresh();

            return;
        }

        $isChange = !is_null($this->getApprovalStatus());
        $oldState = $this->getApprovalStatus();
        /** @var HasApprovalStatuses|BackedEnum $state */
        $state = $this->getActionStatus();

        if ($isChange) {
            $this->getActionBoundApproval()?->delete();
        }

        Approval::create([
            'approver_id' => Auth::id(),
            'approver_type' => User::class,
            'approvable_id' => $this->approvable()->id,
            'approvable_type' => $this->approvable()::class,
            'status' => $state->value,
            'key' => $this->getApprovalKey(),
            'approval_by' => $this->getApprovalBy()->getName(),
        ]);

        if ($isChange) {
            $this->sendNotificationOnChangeApproval($state->value, $oldState);
        } else {
            $this->sendNotificationOnSetApproval($state->value);
        }

        $this->getRecord()?->refresh();
    }

    public function getRecord(): ?Model
    {
        return $this->getRecordFromUsing();
    }

    public function isActionActive(): bool
    {
        return $this->getApprovalStatus() === $this->getActionStatus();
    }

    public function getApprovalStatus(): UnitEnum|string|null
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

    /**
     * The Sate which the Action is bound, as example the 'approve' because it's the approval action
     *
     * @return HasApprovalStatuses|null
     */
    public function getActionStatus(): ?HasApprovalStatuses
    {
        return $this->approvalStatus;
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

        return $this->getApprovalStatus() !== $this->getActionStatus();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->name($this->name);
        $this->action($this->changeApproval(...));
        $this->iconPosition(IconPosition::After);
    }
}
