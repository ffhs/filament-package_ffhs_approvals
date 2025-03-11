<?php

namespace Ffhs\Approvals\Infolists\Actions;

use App\Domain\Approvals\Application\ApplicationApprovalStatus;
use App\Domain\Approvals\Documents\DocumentApprovalStatus;
use App\Models\User;
use BackedEnum;
use Ffhs\Approvals\Concerns\HandlesApprovals;
use Ffhs\Approvals\Contracts\ApprovableByComponent;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Models\Approval;
use Ffhs\Approvals\Traits\HasApprovalNotification;
use Ffhs\Approvals\Traits\HasResetApprovalAction;
use Filament\Infolists\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class ApprovalSingleStateAction extends Action implements ApprovableByComponent
{
    use HandlesApprovals;
    use HasResetApprovalAction;
    use HasApprovalNotification;

    protected ?HasApprovalStatuses $status = null;
    protected array $statusCategoryColors = [
        'approved' => 'success',
        'denied' => 'danger',
        'pending' => 'info',
    ];
    private string|array|null $colorSelected = null;
    private string|array|null $colorNotSelected = null;
    private array $approvalIcons = [];

    public function changeApproval(): void
    {
        if ($this->isActionActive()) {
            $this
                ->getActionBoundApproval()
                ->delete();

            Notification::make()
                ->title('remove approval') //ToDo Translate and add text state
                ->success()
                ->send();

            $this
                ->getRecord()
                ->refresh();

            return;
        }

        $isChange = !is_null($this->getStatus());
        $oldState = $this->getStatus();
        /** @var ApplicationApprovalStatus|DocumentApprovalStatus $state */
        $state = $this->getActionStatus();

        if ($isChange) {
            $this
                ->getActionBoundApproval()
                ->delete();
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

        $this
            ->getRecord()
            ->refresh();
    }

    public function isActionActive(): bool
    {
        return $this->getStatus() === $this->getActionStatus();
    }

    public function getStatus(): ?string
    {
        $status = $this->getActionBoundApproval()?->status;

        if ($status instanceof BackedEnum) {
            return $status->value;
        }

        return $status;
    }

    public function getActionBoundApproval(): ?Approval
    {
        return $this
            ->getBoundApprovals()
            ->first();
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
        return $this->status;
    }

    public function actionStatus(HasApprovalStatuses $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function colorSelected(null|string|array $colorSelected): static
    {
        $this->colorSelected = $colorSelected;

        return $this;
    }

    public function colorNotSelected(null|string|array $colorNotSelected): static
    {
        $this->colorNotSelected = $colorNotSelected;

        return $this;
    }

    public function approvalIcons(array $approvalIcons): static
    {
        $this->approvalIcons = $approvalIcons;

        return $this;
    }

    public function getIcon(): string|Htmlable|null
    {
        /** @var ApplicationApprovalStatus|DocumentApprovalStatus $state */
        $state = $this->getActionStatus();
        $this->icon = $this->approvalIcons[$state->value] ?? null;

        return parent::getIcon();
    }

    public function isDisabled(): bool
    {
        if (($this->evaluate($this->isDisabled) || $this->isHidden()) || !$this->canApprove()) {
            return true;
        }

        if (!$this->isNeedResetApprovalBeforeChange()) {
            return false;
        }

        return !is_null($this->getStatus());
    }

    public function isHidden(): bool
    {
        if (parent::isHidden()) {
            return true;
        }

        if (!$this->isNeedResetApprovalBeforeChange() || is_null($this->getStatus())) {
            return false;
        }

        return $this->getStatus() !== $this->getActionStatus();
    }

    public function getColor(): string|array|null
    {
        /** @var ApplicationApprovalStatus|DocumentApprovalStatus $state */
        $state = $this->getActionStatus();

        if (!$this->isActionActive()) {
            $colors = $this->getColorNotSelected();

            if (!is_array($colors)) {
                return $colors ?? 'gray';
            }

            return $colors[$state->value] ?? 'gray';
        }

        /** @var HasApprovalStatuses $statusEnum */
        $statusEnum = $this
            ->getApprovalFlow()
            ->getStatusEnumClass();
        $colors = $this->getColorSelected();

        if (!is_null($colors)) {
            if (!is_array($colors)) {
                return $colors;
            }
            if (array_key_exists($state->value, $colors)) {
                return $colors[$state->value];
            }
        }

        if (in_array($state, $statusEnum::getApprovedStatuses(), true)) {
            return $this->getApprovedStatusColor();
        }

        if (in_array($state, $statusEnum::getDeniedStatuses(), true)) {
            return $this->getDeniedStatusColor();
        }

        if (in_array($state, $statusEnum::getPendingStatuses(), true)) {
            return $this->getPendingStatusColor();
        }

        return parent::getColor();
    }

    public function getColorNotSelected(): string|array|null
    {
        return $this->evaluate($this->colorNotSelected);
    }

    public function getColorSelected(): null|string|array
    {
        return $this->evaluate($this->colorSelected);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->name($this->name);
        $this->action($this->changeApproval(...));
        $this->iconPosition(IconPosition::After);
    }
}
